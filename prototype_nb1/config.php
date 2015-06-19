<?php

date_default_timezone_set('UTC');
require_once('corelib/safeRequestFunctions.php');
require_once('corelib/templateMerge.php');
include('lib/login.php');
//include_once('lib/libfuncs.php');

$TEMPLATE = 'html/template.html';

$CFG['cookiehash'] = "apehdlywdosoqegjao";
$CFG['cookietimelimit'] =  10800; // seconds
$CFG['appname'] = 'GUIT';

// LDAP server IP
$CFG['ldaphost'] = '130.209.13.173';
// LDAP context or list of contexts
$CFG['ldapcontext'] = 'o=Gla';

// Database settings
$DBCFG['type']='MySQL';
$DBCFG['host']="localhost"; // Host name
$DBCFG['username']="guit"; // Mysql username
$DBCFG['password']="guit"; // Mysql password
$DBCFG['db_name']="guit"; // Database name

?>
