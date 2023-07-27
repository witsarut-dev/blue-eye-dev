<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define("ENDYEAR",5);
define("PAGESIZE",10,true);

define("FB_ACCESS_TOKEN","369272916886191|ea0aaa4dd25013b41d1532b064f2bfcb");

define("TB_OAUTH_ACCESS_TOKEN","837126367779008512-uvZViR2qP1q7AqHkXLkhdCS45jYAlaj");
define("TB_OAUTH_ACCESS_TOKEN_SECRET","oBkvVaKNwAoO1AXIxpJUMKlZ7dJSkAoq4rUrv7jetx6lp");
define("TB_CONSUMER_KEY","569OemAV21SyQngN9V05d1Box");
define("TB_CONSUMER_SECRET","aWNnGCx5R2LD0T1ngTou0xzlRwelAa9YfrWFBCqV0ezkHamk5T");

#define("MONGO_CONNECTION", "mongodb://127.0.0.1");
define("MONGO_CONNECTION", "mongodb://blueeyeadmin:BEfront3075@10.130.72.139:34596");
define("S_SENSE_KEY", "17f363eb609961ebb0df94ea616f7736");

define("ROOT_PATH","/opt/lampp/htdocs/blue-eye/index.php task");
define("PHP_PATH","/opt/lampp/bin/php");

define("HIGHCHARTS_SERVER","http://10.130.18.192:8889/");

/* End of file constants.php */
/* Location: ./application/config/constants.php */
