<?php
/*
 * jQuery File Upload Plugin PHP Class 6.6.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

require(MAIN.'/ext/UploadHandler.php');
 
class UploadHandlerExt extends UploadHandler
{
	 // function __construct($options = null, $initialize = true, $error_messages = null) {
        // $this->options = array(
            // 'script_url' => $this->get_full_url().'/',
            // 'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/files/',
            // 'upload_url' => $this->get_full_url().'/files/',
            // 'user_dirs' => false,
            // 'mkdir_mode' => 0755,
            // 'param_name' => 'files',
            // // Set the following option to 'POST', if your server does not support
            // // DELETE requests. This is a parameter sent to the client:
            // 'delete_type' => 'DELETE',
            // 'access_control_allow_origin' => '*',
            // 'access_control_allow_credentials' => false,
            // 'access_control_allow_methods' => array(
                // 'OPTIONS',
                // 'HEAD',
                // 'GET',
                // 'POST',
                // 'PUT',
                // 'PATCH',
                // 'DELETE'
            // ),
            // 'access_control_allow_headers' => array(
                // 'Content-Type',
                // 'Content-Range',
                // 'Content-Disposition'
            // ),
            // // Enable to provide file downloads via GET requests to the PHP script:
            // //     1. Set to 1 to download files via readfile method through PHP
            // //     2. Set to 2 to send a X-Sendfile header for lighttpd/Apache
            // //     3. Set to 3 to send a X-Accel-Redirect header for nginx
            // // If set to 2 or 3, adjust the upload_url option to the base path of
            // // the redirect parameter, e.g. '/files/'.
            // 'download_via_php' => false,
            // // Read files in chunks to avoid memory limits when download_via_php
            // // is enabled, set to 0 to disable chunked reading of files:
            // 'readfile_chunk_size' => 10 * 1024 * 1024, // 10 MiB
            // // Defines which files can be displayed inline when downloaded:
            // 'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
            // // Defines which files (based on their names) are accepted for upload:
            // 'accept_file_types' => '/.+$/i',
            // // The php.ini settings upload_max_filesize and post_max_size
            // // take precedence over the following max_file_size setting:
            // 'max_file_size' => null,
            // 'min_file_size' => 1,
            // // The maximum number of files for the upload directory:
            // 'max_number_of_files' => null,
            // // Image resolution restrictions:
            // 'max_width' => null,
            // 'max_height' => null,
            // 'min_width' => 1,
            // 'min_height' => 1,
            // // Set the following option to false to enable resumable uploads:
            // 'discard_aborted_uploads' => true,
            // // Set to true to rotate images based on EXIF meta data, if available:
            // 'orient_image' => false,
            // 'image_versions' => array(
                // // Uncomment the following version to restrict the size of
                // // uploaded images:
                // /*
                // '' => array(
                    // 'max_width' => 1920,
                    // 'max_height' => 1200,
                    // 'jpeg_quality' => 95
                // ),
                // */
                // // Uncomment the following to create medium sized images:
                // /*
                // 'medium' => array(
                    // 'max_width' => 800,
                    // 'max_height' => 600,
                    // 'jpeg_quality' => 80
                // ),
                // */
                // 'thumbnail' => array(
                    // // Uncomment the following to force the max
                    // // dimensions and e.g. create square thumbnails:
                    // //'crop' => true,
                    // 'max_width' => 80,
                    // 'max_height' => 80
                // )
            // )
        // );
        // if ($options) {
            // $this->options = array_merge($this->options, $options);
        // }
        // if ($error_messages) {
            // $this->error_messages = array_merge($this->error_messages, $error_messages);
        // }
        // if ($initialize) {
            // $this->initialize();
        // }
    // }

	public function post($print_response = true) {
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->delete($print_response);
        }
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;
        // Parse the Content-Disposition header, if available:
        $file_name = $this->get_server_var('HTTP_CONTENT_DISPOSITION') ?
            rawurldecode(preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $this->get_server_var('HTTP_CONTENT_DISPOSITION')
            )) : null;
        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $content_range = $this->get_server_var('HTTP_CONTENT_RANGE') ?
            preg_split('/[^0-9]+/', $this->get_server_var('HTTP_CONTENT_RANGE')) : null;
        $size =  $content_range ? $content_range[3] : null;
        $files = array();
        if ($upload && is_array($upload['tmp_name'])) {
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ($upload['tmp_name'] as $index => $value) {
            	$file_name = $this->get_new_name();
                $files[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    $file_name ? $file_name : $upload['name'][$index],
                    $size ? $size : $upload['size'][$index],
                    $upload['type'][$index],
                    $upload['error'][$index],
                    $index,
                    $content_range
                );
            }
        } else {
            // param_name is a single object identifier like "file",
            // $_FILES is a one-dimensional array:
        	$file_name = $this->get_new_name();
            $files[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                $file_name ? $file_name : (isset($upload['name']) ?
                        $upload['name'] : null),
                $size ? $size : (isset($upload['size']) ?
                        $upload['size'] : $this->get_server_var('CONTENT_LENGTH')),
                isset($upload['type']) ?
                        $upload['type'] : $this->get_server_var('CONTENT_TYPE'),
                isset($upload['error']) ? $upload['error'] : null,
                null,
                $content_range
            );
        }
        return $this->generate_response(
            array($this->options['param_name'] => $files),
            $print_response
        );
    }
	
    public function generate_response($content, $print_response = false) {
        if ($print_response) {
            $json = json_encode($content);
            $redirect = isset($_REQUEST['redirect']) ?
                stripslashes($_REQUEST['redirect']) : null;
            if ($redirect) {
                $this->header('Location: '.sprintf($redirect, rawurlencode($json)));
                return;
            }
            $this->head();
            if ($this->get_server_var('HTTP_CONTENT_RANGE')) {
                $files = isset($content[$this->options['param_name']]) ?
                    $content[$this->options['param_name']] : null;
                if ($files && is_array($files) && is_object($files[0]) && $files[0]->size) {
                    $this->header('Range: 0-'.(
                        $this->fix_integer_overflow(intval($files[0]->size)) - 1
                    ));
                }
            }
            // $this->body($json);
        }
        return $content;
    }
	
	
	private function get_new_name( $l = 10 ){
	    $key = '';
	    $keys = array_merge(range(0, 9), range('a', 'z'));
	    for ($i = 0; $i < $l; $i++) {
	        $key .= $keys[array_rand($keys)];
		}
		if(file_exists(MAIN."/files/".$key.".png") || file_exists(MAIN."/files/".$key.".gif") || file_exists(MAIN."/files/".$key.".jpg") ){
			return $this->GetName($l);
		}else{
	    	return $key;
		}
	}

}
