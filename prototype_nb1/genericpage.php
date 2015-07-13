<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

$template = new templateMerge($TEMPLATE);

$uinfo = checkLoggedInUser();

$template->pageData['pagetitle'] = 'University of Glasgow '; //# append conference name
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

	$template->pageData['mainBody'] = '<pre>'.print_r($uinfo,1).'</pre>';
	$template->pageData['logoutLink'] = loginBox($uinfo);
	
	$template->pageData['sideInfo'] = "<h2> Menu </h2> <br> <a href='/prototype_nb1/main.php'>Main Page</a> <br></br> <a href='/prototype_nb1/badges.php'>Badges</a>";


}

echo $template->render();


?>