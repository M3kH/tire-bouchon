<?php
global $config;
if(!isset($config)){
	$config = array();
}

$config['api'] = array();
$config['api']['route'] = array();

$config['api']['route']["user/new/"] = array();
$config['api']['route']["user/new/"]["class"] = "Login";
$config['api']['route']["user/new/"]["fnc"] = "CreateUser";
$config['api']['route']["user/new/"]["method"] = "post";

$config['api']['route']["user/login/"] = array();
$config['api']['route']["user/login/"]["class"] = "Login";
$config['api']['route']["user/login/"]["fnc"] = "Authorization";
$config['api']['route']["user/login/"]["method"] = "post";

$config['api']['route']["user/social/"] = array();
$config['api']['route']["user/social/"]["class"] = "Login";
$config['api']['route']["user/social/"]["fnc"] = "SocialLogin";
$config['api']['route']["user/social/"]["method"] = "get";

$config['api']['route']["tags/"] = array();
$config['api']['route']["tags/"]["class"] = "Tags";
$config['api']['route']["tags/"]["fnc"] = "GetAll";
$config['api']['route']["tags/"]["method"] = "get";

$config['api']['route']["links/"] = array();
$config['api']['route']["links/"]["class"] = "Links";
$config['api']['route']["links/"]["fnc"] = "GetAll";
$config['api']['route']["links/"]["method"] = "get";

$config['api']['route']["links/random/"] = array();
$config['api']['route']["links/random/"]["class"] = "Links";
$config['api']['route']["links/random/"]["fnc"] = "InsertRandom";
$config['api']['route']["links/random/"]["method"] = "get";

$config['api']['route']["links/new/"] = array();
$config['api']['route']["links/new/"]["class"] = "Links";
$config['api']['route']["links/new/"]["fnc"] = "NewLink";
$config['api']['route']["links/new/"]["method"] = "post";

$config['api']['route']["links/update/"] = array();
$config['api']['route']["links/update/"]["class"] = "Links";
$config['api']['route']["links/update/"]["fnc"] = "Update";
$config['api']['route']["links/update/"]["method"] = "post";

$config['api']['route']["upload/"] = array();
$config['api']['route']["upload/"]["class"] = "Upload";
$config['api']['route']["upload/"]["fnc"] = "UploadFile";
$config['api']['route']["upload/"]["method"] = "post";

$config['api']['route']["ide/dir"] = array();
$config['api']['route']["ide/dir"]["class"] = "Ide";
$config['api']['route']["ide/dir"]["fnc"] = "GetDir";
$config['api']['route']["ide/dir"]["method"] = "get";

$config['api']['route']["ide/file"] = array();
$config['api']['route']["ide/file"]["class"] = "Ide";
$config['api']['route']["ide/file"]["fnc"] = "GetFile";
$config['api']['route']["ide/file"]["method"] = "get";
