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
$userID = $dbUser->id;

$template->pageData['pagetitle'] = 'University of Glasgow GUIT';
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] .= "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";


if($uinfo==false)
{
	$template->pageData['headings'] .= "<h1  style='text-align:center; padding:10px;'>GUID login</h1>";
	$template->pageData['loginBox'] = loginBox($uinfo);
	if(file_exists('logininfo.htm	'))
		$template->pageData['mainBody'] = file_get_contents('logininfo.htm').'<br/>';
}
else
{
	$username = $uinfo['uname'];

	//Menu
	$template->pageData['sideInfo'] .= "<h2> Menu </h2>";
	$template->pageData['sideInfo'] .= "<a class='menuItem' href='/index.php'>Home</a> ";
	$template->pageData['sideInfo']	.= "<a class='menuItem' href='/browse.php'>Browse Teaching Practices</a>";
	$template->pageData['sideInfo']	.= "<a class='menuItem' href='/addbundle.php'>New Teaching Practice</a>";
	$template->pageData['sideInfo'] .= "<a class='menuItem' href='/user_profile.php?profile=$userID'>My Profile</a> ";
	$template->pageData['sideInfo'] .= "<a class='menuItem' href='/badges.php'>Badges</a>";
	if ($dbUser->isadmin == true) {
		$template->pageData['sideInfo'] .= "<a class='menuItem' href='/manageusers.php'>Manage Users</a>";
	}


	//Badges
	$template->pageData['mainBody'].= "<div id='badges'>";
	$query = "SELECT * FROM badge, user_earns_badge, user WHERE user_earns_badge.user_id = user.id AND user.username = '$username' AND badge.id = user_earns_badge.badge_id;";
	$badges =  dataConnection::runQuery($query);
	$template->pageData['mainBody'] .= "<h2>My Badges</h2>";
	if($badges != false){
			foreach($badges as $b)
			{
				$template->pageData['mainBody'] .= "<img src = {$b['url']} alt = 'badge'>";
			}
			$template->pageData['mainBody'] .= "<br><br>";
	}
	else{
		$template->pageData['mainBody'] .= "You do not have any badges yet.";
	}
	$template->pageData['mainBody'].= "</div>";
	    

	//Score
	$template->pageData['mainBody'].= "<div id='score'>";
	$template->pageData['mainBody'] .= "<h2>My Score</h2><br>";
	$likePoints = likePoints($userID);
	$likedPoints = likedPoints($userID);
	$commentPoints = commentPoints($userID);
	$postPoints = postPoints($userID);
	$sum = $likePoints + $likedPoints + $commentPoints +$postPoints;
	$template->pageData['mainBody'] .= "<table>";
	$template->pageData['mainBody'] .= "<tr><td>Liking Teaching Practices</td><td>$likePoints</td></tr>";
	$template->pageData['mainBody'] .= "<tr class='alt'><td>Likes in your Teaching Practices</td><td>$likedPoints</td></tr>";
	$template->pageData['mainBody'] .= "<tr><td>Commenting Teaching Practices</td><td>$commentPoints</td></tr>";
	$template->pageData['mainBody'] .= "<tr class='alt'><td>Posting Teaching Practices</td><td>$postPoints</td></tr>";
	$template->pageData['mainBody'] .= "<tr><td></td><td></td></tr>";
	$template->pageData['mainBody'] .= "<tr><td>Total points: </td><td>$sum</td></tr>";
	$template->pageData['mainBody'] .= "</table>";

	$template->pageData['mainBody'].= "</div>";
	    
	
				
    //Teaching Practices created by the user
	$template->pageData['mainBody'].= "<div id='myTeachingPractices'>";
    $user_bundles = teachingpractice::retrieve_teachingpractice_matching('author_id', $dbUser->id);
	$template->pageData['mainBody'] .= "<br><h2>My Teaching Practices</h2>";
    if($user_bundles !== false) {
    	$template->pageData['mainBody'] .="<ul>";
	    foreach($user_bundles as $b) {
	        $template->pageData['mainBody'] .= "<li><a href='view_teaching_practice.php?tpID= {$b->id}'>{$b->title}</a></li>";
		}
	    $template->pageData['mainBody'] .= "</ul>";
    }else{
		$template->pageData['mainBody'] .= "You have not posted any teaching practices yet.";
	}
	$template->pageData['mainBody'].= "</div>";
    

	//Latest Teaching Practices
	$template->pageData['mainBody'].= "<div id='latestTeachingPractices'>";
	$template->pageData['mainBody'] .= "<br><h2>Latest Teaching Practices</h2>";
	$query = "SELECT * FROM teachingpractice ORDER BY id DESC";
	$bundles = dataConnection::runQuery($query);
	if($bundles !== false) {
		$template->pageData['mainBody'] .="<ul>";
		$counter = 0;
		$max = 5;
		foreach($bundles as $b) {
			$template->pageData['mainBody'] .= "<li><a href='view_teaching_practice.php?tpID= {$b['id']} '>{$b['title']}</a></li>";
			$counter++;
			if ($counter > ($max - 1)) break;
		}
		$template->pageData['mainBody'] .= "</ul>";
	} else {
		$template->pageData['mainBody'] .= "No Teaching Practices have been posted yet.";
	}
	$template->pageData['mainBody'].= "</div>";

    

    $template->pageData['logoutLink'] = loginBox($uinfo);
}

//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";

?>