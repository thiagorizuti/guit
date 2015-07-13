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

$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";


$template->pageData['pagetitle'] = 'University of Glasgow GUIT';
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] .= "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";


//$template->pageData['breadcrumb'] .= '| <a href="index.php">Abstracts</a> | <a href="admin.php">Admin home</a>';
if($uinfo==false)
{
	$template->pageData['headings'] .= "<h1  style='text-align:center; padding:10px;'>GUID login</h1>";
	$template->pageData['loginBox'] = loginBox($uinfo);
    	if(file_exists('logininfo.htm'))
	    $template->pageData['mainBody'] = file_get_contents('logininfo.htm').'<br/>';
}
else
{

	$username = $uinfo['uname'];
	
		$template->pageData['buttons'] .= "$spaces $spaces $spaces $spaces <a href='/prototype_nb1/user_profile.php?profile= $userID'>My Profile</a> ";
	    
	    	    	
	//Printing Badges
	$query = "SELECT * FROM badge, user_earns_badge, user WHERE user_earns_badge.user_id = user.id AND user.username = '$username' AND badge.id = user_earns_badge.badge_id;";
	$badges =  dataConnection::runQuery($query);

	if($badges != false)	
	    {
	    $template->pageData['mainBody'] .= "<h2>Badges</h2>";
		    foreach($badges as $b)
		    {
		        $template->pageData['mainBody'] .= "<img src = {$b['url']} alt = 'badge'>"; 
		    }
		    $template->pageData['mainBody'] .= "<br><br>";
	    }	
	    

	//Printing Points
	$template->pageData['mainBody'] .= "<h2>Points</h2><br>";
	$query = "SELECT * FROM user WHERE username = '$username';";
	$points =  dataConnection::runQuery($query);

	if($points !== false)
	{
		    foreach($points as $p)
		    {
		        $sum = $p['points_likes'] + $p['points_liked'] + $p['points_comments'] + $p['points_bundles'];
			
			$template->pageData['mainBody'] .= "Total points: $sum<br>";
			if ($sum != 0)
			{			
				$template->pageData['mainBody'] .= "Points from Liking Teaching Practices: {$p['points_likes']}<br>";
				$template->pageData['mainBody'] .= "Points from Likes in your Teaching Practices: {$p['points_liked']}<br>";
				$template->pageData['mainBody'] .= "Points from Commentating Teaching Practices: {$p['points_comments']} <br>";
				$template->pageData['mainBody'] .= "Points from Posting Teaching Practices: {$p['points_bundles']} <br>";
			}
		    }
	    }    
	    
	
				
    //Printing Teaching Practices created by the user	

    $user_bundles = teachingpractice::retrieve_teachingpractice_matching('author_id', $dbUser->id);
    if($user_bundles !== false)
    {
    $template->pageData['mainBody'] .= "<br><h2>My Teaching Practices</h2>";
    $template->pageData['mainBody'] .="<ul>";
	    foreach($user_bundles as $b)
	    {
	        $template->pageData['mainBody'] .= "<li><a href='view_teaching_practice.php?sessionID= {$b->id}'>{$b->title}</a></li>";
	    }
	    	    
	    $template->pageData['mainBody'] .= "</ul>";
    }
    
        //Printing Latest 5 Teaching Practices posted	

	$template->pageData['mainBody'] .= "<br><h2>Latest Teaching Practices</h2>";
        $query = "SELECT * FROM teachingpractice ORDER BY id DESC";
	$bundles = dataConnection::runQuery($query);
        if($bundles !== false)
        {
        $template->pageData['mainBody'] .="<ul>";
	
	$counter = 0;
	$max = 5;
    	    foreach($bundles as $b)
    	    {        	      			
    	        $template->pageData['mainBody'] .= "<li><a href='view_teaching_practice.php?sessionID= {$b['id']} '>{$b['title']}</a></li>";
		
		$counter++;
		if ($counter > ($max - 1)) break;
    	    }
    	    	    
    	    $template->pageData['mainBody'] .= "</ul>";
        }
	else $template->pageData['mainBody'] .= "No Teaching Practices have been posted yet.";
    
    
    $template->pageData['mainBody'] .= "<h2>Create a Teaching Practice</h2> <p><a href='addbundle.php'>new bundle</a></p>";
    
    

    
    if ($dbUser->isadmin == true) { // user is admin

        $template->pageData['sideInfo'] .= "<h2> Menu </h2>
            <br> <a href='/prototype_nb1/badges.php'>Badges</a>
            <br> <a href='/prototype_nb1/manageusers.php'>Manage Users</a>";
    	
    }else{
        $template->pageData['sideInfo'] .= "<h2> Menu </h2> <br> <a href='/prototype_nb1/badges.php'>Badges</a> ";
    }

    $template->pageData['logoutLink'] = loginBox($uinfo);
}


//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";

?>