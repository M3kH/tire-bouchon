<?php
global $config;
if(!isset($config)){
	$config = array();
}

$config['api'] = array();
$config['api']['route'] = array();

$config['api']['route']["users/"] = array();
$config['api']['route']["users/"]["class"] = "Users";
$config['api']['route']["users/"]["fnc"] = "GetAll";
$config['api']['route']["users/"]["method"] = "get";

$config['api']['route']["users/"]["get"]["class"] = "Users";
$config['api']['route']["users/"]["get"]["fnc"] = "GetAll";

$config['api']['route']["users/"]["get"]["class"] = "Users";
$config['api']['route']["users/:id"]["get"]["fnc"] = "Get";

$config['api']['route']["users/"]["post"]["class"] = "Users";
$config['api']['route']["users/"]["post"]["fnc"] = "Insert";

$config['api']['route']["users/"]["put"]["class"] = "Users";
$config['api']['route']["users/"]["put"]["fnc"] = "GetAll";

$config['api']['route']["users/"]["delete"]["class"] = "Users";
$config['api']['route']["users/"]["delete"]["fnc"] = "Delete";

$config['api']['route']["user/new/"] = array();
$config['api']['route']["user/new/"]["post"]["class"] = "Login";
$config['api']['route']["user/new/"]["post"]["fnc"] = "CreateUser";

$config['api']['route']["user/login/"]["post"] = array();
$config['api']['route']["user/login/"]["post"]["class"] = "Login";
$config['api']['route']["user/login/"]["post"]["fnc"] = "Authorization";

$config['api']['route']["user/social/"] = array();
$config['api']['route']["user/social/"]["class"] = "Login";
$config['api']['route']["user/social/"]["fnc"] = "SocialLogin";
$config['api']['route']["user/social/"]["method"] = "get";

$config['api']['route']["ide/dir"] = array();
$config['api']['route']["ide/dir"]["class"] = "Ide";
$config['api']['route']["ide/dir"]["fnc"] = "GetDir";
$config['api']['route']["ide/dir"]["method"] = "get";

$config['api']['route']["ide/file"] = array();
$config['api']['route']["ide/file"]["class"] = "Ide";
$config['api']['route']["ide/file"]["fnc"] = "GetFile";
$config['api']['route']["ide/file"]["method"] = "get";
