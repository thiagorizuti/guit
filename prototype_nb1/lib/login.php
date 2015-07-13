<?php
require_once('lib/guldap_login.php');

function checkLoggedInUser($allowLogin = true)
{
	global $CFG;
    $uinfo = false;
	if(($allowLogin )&&(isset($_REQUEST['uname']))&&(isset($_REQUEST['pwd'])))
    {
        $uinfo = checkLogin($_REQUEST['uname'], $_REQUEST['pwd']);
    }
    elseif(isset($_REQUEST['logout']))
    {
        setcookie($CFG['appname'].'_login','');
        return false;
    }
    elseif(isset($_COOKIE[$CFG['appname'].'_login']))
    {
        $uinfo = CheckValidLoginCookie($_COOKIE[$CFG['appname'].'_login']);
    }
    if($uinfo)
    {
      	setcookie($CFG['appname'].'_login',CreateLoginCookie($uinfo));
        return $uinfo;
    }
    else
    {
    	return false;
    }
}

function CreateLoginCookie($uinfo)
{
	global $CFG;
    $cookieinfo = base64_encode(serialize($uinfo));
    $cookie = implode('@', array($cookieinfo,time()+$CFG['cookietimelimit']));
    $cookie = $cookie .'::'.md5($cookie.$CFG['cookiehash']);
    return $cookie;
}

function CheckValidLoginCookie($cookie)
{
	global $CFG;
  	list($cookie,$hash) = explode('::',$cookie,2);
    if(trim(md5($cookie.$CFG['cookiehash']))==trim($hash))
    {
      	list($cookieinfo, $t) = explode('@',$cookie,2);
      	if(intval($t) > time())
        {
            return unserialize(base64_decode($cookieinfo));
        }
    }
   	return false;
}

function loginBox($uinfo)
    {
	$out ='<div class="loginBox">';
    if($uinfo==false)
        {
		$out .= "<form method='POST' action='http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."'>";
	    $out .= "<table><tr><td><label for='uname'>GUID</label>:</td><td><input type='text' name='uname' id='uname'/></td></tr>";
	    $out .= "<tr><td><label for='pwd'>Password</label>:</td><td><input type='password' name='pwd' id='pwd'/></td></tr>";
	    $out .= "<tr><td colspan='2' align='center'><input type='submit' name='submit' value='Log-in'/></td></tr></table></form>";
        }
        else
    	$out .= "You are logged in as {$uinfo['gn']} {$uinfo['sn']} (<a href='{$_SERVER['PHP_SELF']}?logout=1'>Log-out</a>)";
    $out .= '</div>';
    return $out;
}


?>