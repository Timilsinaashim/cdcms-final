<?php
$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');
$server_ip = $_SERVER['SERVER_ADDR'];
if(!defined('base_url')) define('base_url', 'http://' . $_SERVER['HTTP_HOST'] . '/');

// if(!defined('base_url')) define('base_url','http://3.91.139.179/');
if(!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/' );
if(!defined('dev_data')) define('dev_data',$dev_data);

// php test
// if(!defined('DB_SERVER')) define('DB_SERVER',"project-php.ca6crwpmxkgt.us-east-1.rds.amazonaws.com");
//  if(!defined('DB_USERNAME')) define('DB_USERNAME',"admin");
//  if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"adminadmin");
// if(!defined('DB_NAME')) define('DB_NAME',"cdcms");

// php prod
if(!defined('DB_SERVER')) define('DB_SERVER',"project-babysitter.ca6crwpmxkgt.us-east-1.rds.amazonaws.com");
 if(!defined('DB_USERNAME')) define('DB_USERNAME',"admin");
 if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"adminadmin");
if(!defined('DB_NAME')) define('DB_NAME',"cdcms");

// if(!defined('DB_SERVER')) define('DB_SERVER',"localhost");
//  if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
//  if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
// if(!defined('DB_NAME')) define('DB_NAME',"cdcms_db");
?>


