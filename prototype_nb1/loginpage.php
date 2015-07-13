<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');
require_once('lib/database.php');
require_once('lib/sharedfunctions.php');
require_once('corelib/dataaccess.php');
$template = new templateMerge($TEMPLATE);

$uinfo = checkLoggedInUser();
$dbUser = getUserRecord($uinfo);

$template->pageData['pagetitle'] = 'University of Glasgow GUIT'; //# append conference name
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] = "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";
//$template->pageData['breadcrumb'] .= '| <a href="index.php">Abstracts</a> | <a href="admin.php">Admin home</a>';
if($uinfo==false)
{
	$template->pageData['headings'] = "<h1  style='text-align:center; padding:10px;'>GUID login</h1>";
	$template->pageData['loginBox'] = loginBox($uinfo);
    if(file_exists('logininfo.htm'))
	    $template->pageData['mainBody'] = file_get_contents('logininfo.htm').'<br/>';
}
else
{
	$username = $uinfo['uname'];
	$query = "SELECT * FROM badge, user_earns_badge, user WHERE user_earns_badge.user_id = user.id AND user.username = '$username' AND badge.id = user_earns_badge.badge_id;";
	$badges =  dataConnection::runQuery($query);

	if($badges !== false)
	$template->pageData['mainBody'] .= "<h2>Badges</h2>";	
	    {
		    foreach($badges as $b)
		    {
		        $template->pageData['mainBody'] .= "<img src = {$b['url']} alt = 'badge'>"; 
			// $template->pageData['mainBody'] .= '<pre>'.print_r($b,1).'<pre>';
		    }
	    }	    
	    
	$template->pageData['logoutLink'] = loginBox($uinfo);
	
	
	$template->pageData['sideInfo'] .= "<h2> Menu </h2> <br> <a href='/prototype_nb1/badges.php'>Badges</a>";
	

	if ($uinfo['isadmin'] == 1) { // user is admin
	
	//$template->pageData['sideInfo'] .= "is admin";
	
	
	}


}

echo $template->render();


?>