<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');
require_once('lib/database.php');
require_once('lib/sharedfunctions.php');
require_once('lib/addbundle_form.php');

$template = new templateMerge($TEMPLATE);

$uinfo = checkLoggedInUser();
$dbUser = getUserRecord($uinfo);
$userID = $dbUser->id;

$template->pageData['pagetitle'] = 'University of Glasgow GUIT';
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] = "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";

$template->pageData['sideInfo'] .= "<h2> Menu </h2>";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/index.php'>Home</a> ";
$template->pageData['sideInfo']	.= "<a class='menuItem' href='/browse.php'>Browse Teaching Practices</a>";
$template->pageData['sideInfo']	.= "<a class='menuItem' href='/addbundle.php'>New Teaching Practice</a>";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/user_profile.php?profile=$userID'>My Profile</a> ";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/badges.php'>Badges</a>";
if ($dbUser->isadmin == true) {
	$template->pageData['sideInfo'] .= "<a class='menuItem' href='/manageusers.php'>Manage Users</a>";
}


if($uinfo==false)
{
	header("Location: index.php");
    exit();
}
else
{

	//Example of use of form addbundle_form
	$exampleform = new addbundle_form();
	switch($exampleform->getStatus())
	{
	case FORM_NOTSUBMITTED:
	    //$exampleform->setData($existingdata);
	    $template->pageData['mainBody'] = $exampleform->getHtml();
	    break;
	case FORM_SUBMITTED_INVALID:
	    $template->pageData['mainBody'] = $exampleform->getHtml();
	    break;
	case FORM_SUBMITTED_VALID:
	    $data = new teachingpractice();
	    $exampleform->getData($data);
        $data->time = time();
        $data->author_id = $dbUser->id;
        $data->insert();
	
	//updates points from posting teaching practices
	
	$tempquery = dataConnection::runQuery("SELECT * FROM user WHERE id = '$dbUser->id';");	
		
	foreach($tempquery as $t)
	{
		dataConnection::runQuery("UPDATE user SET points_bundles = (points_bundles + $points_bundles) WHERE id = '{$t['id']}';");	
	}
	
	    // Do stuff with $data
	    // A redirect is likely here, e.g. header('Location:document.php?id='.$data->id);
	    header('Location:index.php');
	    break;
	case FORM_CANCELED:
	    header('Location:index.php');
	    break;
	}

	$template->pageData['logoutLink'] = loginBox($uinfo);
	
}

//if(error_get_last()==null)
    echo $template->render();
//else
//{
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";
//    print_r(error_get_last());
//}


?>