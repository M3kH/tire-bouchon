<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return 
	array(
		"base_url" => URL."ext/hybridauth/", 

		"providers" => array ( 
			// openid providers
			"OpenID" => array (
				"enabled" => true
			),

			// "Yahoo" => array ( 
				// "enabled" => true,
				// "keys"    => array ( "id" => "", "secret" => "" ),
			// ),
// 
			// "AOL"  => array ( 
				// "enabled" => true 
			// ),

			"Google" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "712574621375.apps.googleusercontent.com", "secret" => "Z5lRmR1923yPSqW6vs1bHF2f" ), 
			),

			"Facebook" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "599479786765585", "secret" => "89280d1a6bf19e4632831d08ddec56d6" ), 
				"scope"   => "email, user_about_me", // optional
				//"display" => "page" // optional
			),

			"Twitter" => array ( 
				"enabled" => true,
				"keys"    => array ( "key" => "tU0XnoYCshFzM28XBEFg0g", "secret" => "9LY8zT8foRLrjDMpVWAHWwtcIksODdRttInpavHoMk" ) 
			),

			// windows live
			// "Live" => array ( 
				// "enabled" => true,
				// "keys"    => array ( "id" => "", "secret" => "" ) 
			// ),
// 
			// "MySpace" => array ( 
				// "enabled" => true,
				// "keys"    => array ( "key" => "", "secret" => "" ) 
			// ),
// 
			// "LinkedIn" => array ( 
				// "enabled" => true,
				// "keys"    => array ( "key" => "", "secret" => "" ) 
			// ),
// 
			// "Foursquare" => array (
				// "enabled" => true,
				// "keys"    => array ( "id" => "", "secret" => "" ) 
			// ),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);
