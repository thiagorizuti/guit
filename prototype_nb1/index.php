<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');
require_once('lib/database.php');
require_once('lib/sharedfunctions.php');
$template = new templateMerge($TEMPLATE);

$uinfo = checkLoggedInUser();
$dbUser = getUserRecord($uinfo);

$template->pageData['pagetitle'] = 'University of Glasgow GUIT';
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
	//$template->pageData['mainBody'] = '<pre>'.print_r($uinfo,1).'</pre>';
    $template->pageData['mainBody'] = "<p><a href='addbundle.php'>Add a new bundle</a></p>";
    $template->pageData['mainBody'] .= "<h2>My Bundles</h2><ul>";
    $bundles = teachingpractice::retrieve_teachingpractice_matching('author_id', $dbUser->id);
    if($bundles !== false)
    {
	    foreach($bundles as $b)
	    {
	        $template->pageData['mainBody'] .= "<li>{$b->title}</li>\n";
	    }
    }
    $template->pageData['mainBody'] .= "</ul>";
	$template->pageData['logoutLink'] = loginBox($uinfo);
}

if(error_get_last()==null)
    echo $template->render();
else
    echo "<p>Not rendering template to avoid hiding error messages.</p>";

?>
