<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');
require_once('lib/database.php');
require_once('lib/sharedfunctions.php');
require_once('corelib/dataaccess.php');

require_once('lib/add_comment.php');


$template = new templateMerge($TEMPLATE);

$uinfo = checkLoggedInUser();
$dbUser = getUserRecord($uinfo);
$userID = $dbUser->id;

$points_likes = 1;
$points_liked = 2;
$points_comments = 5;
$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";


$template->pageData['pagetitle'] = 'University of Glasgow GUIT';
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] = "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";
//$template->pageData['breadcrumb'] .= '| <a href="index.php">Abstracts</a> | <a href="admin.php">Admin home</a>';
if($uinfo==false)
{
	header("Location: index.php");
	exit();
}

else
{
        if(isset($_GET['sessionID']))
	    $tp_id = $_GET['sessionID'];
	elseif(isset($_REQUEST['teachingpractice_id']))
	    $tp_id = $_REQUEST['teachingpractice_id'];
	else
            header("Location: index.php");

	// Teaching Practice
	    
	$template->pageData['mainBody'] .= "<h2>Teaching Practice</h2><ul>";
	
	$query = "SELECT * FROM teachingpractice WHERE id = '$tp_id';";		
	$teachingpractice =  dataConnection::runQuery($query);
	
	$template->pageData['buttons'] .= "$spaces $spaces $spaces $spaces <a href='view_teaching_practice.php?sessionID=$tp_id&like=true'> <img src='http://us-cms.jotservers.com/uploads/help/document/joey/88_facebook_like_button_big.jpeg' alt = 'like button' height='50' width='80'> </a><br>";
		    
		
		

	if($teachingpractice !== false)
	    {
		    foreach($teachingpractice as $t)
		    {
		    	// Like button
			    
		    	if (isset($_GET['like'])) {
			    
			    dataConnection::connect();
			    
		    	    $already_liked = mysql_fetch_array(mysql_query("SELECT * FROM user_likes_teachingpractice WHERE user_id = '$userID' AND teachingpractice_id = '$tp_id'"));
		    	    
		    	    if ($already_liked == false)
		    	    {
				$new_id = 0;
				
				$query = mysql_query("SELECT * FROM user_likes_teachingpractice ORDER BY id DESC;");
				$last_id = mysql_fetch_array($query);
				if ($last_id != false)
				{
					$new_id = ($last_id['id'] + 1);
				}
								
		    	    	$time = strftime("%Y-%m-%d", time());
					
		    	    	dataConnection::runQuery("INSERT INTO user_likes_teachingpractice VALUES ('$new_id','$dbUser->id','$tp_id','$time')");
					
				dataConnection::runQuery("UPDATE user SET points_likes = (points_likes + $points_likes) WHERE id = $userID;");
				dataConnection::runQuery("UPDATE user SET points_liked = (points_liked + $points_liked) WHERE id = {$t['author_id']};");		
		    	    }
		    	  }
			      
			// Printing Teaching Practice
			      
			$author = dataConnection::runQuery("SELECT * FROM user WHERE id = '{$t['author_id']}';");
		    		    
				foreach($author as $a)    ;
				{
			
			$template->pageData['mainBody'] .= "<b>Author</b>:";

                    $d = dir("images");
                    $image = 0;

                    while (false !== ($entry = $d->read()))

                    {

                        if (strpos($entry, $userID) !== false)

                        {

                            $template->pageData['mainBody'] .= "<img src='images/{$entry}' height='100' width='100'/>";
                            $image = 1;

                        }
                    }

                    if ($image == 0) $template->pageData['mainBody'] .= "<br><img src='http://www.gamerguides.com/assets/images/profiles/3/83673-71555-profil-300x189gif.jpg' height='100' width='100'/>";


                    $template->pageData['mainBody'] .= "<br>{$a['name']} {$a['lastname']} (<a href='/prototype_nb1/user_profile.php?profile={$a['id']}'>{$a['username']}</a>)<br><br>";
				}
				
			$template->pageData['mainBody'] .= "<b>Title</b>: <br>{$t['title']} <br><br>";			
			$template->pageData['mainBody'] .= "<b>Published time</b>: <br>{$t['time']} <br><br>";
			$template->pageData['mainBody'] .= "<b>Problem addressed</b>: {$t['problemstatement']} <br>";
			$template->pageData['mainBody'] .= "<b>Description</b>: {$t['thisbundle']} <br>";
			$template->pageData['mainBody'] .= "<b>How it works</b>: {$t['wayitworks']} <br>";
			$template->pageData['mainBody'] .= "<b>Ways to make it work better</b>: {$t['worksbetter']} <br>";
			$template->pageData['mainBody'] .= "<b>Things that stip it working</b>: {$t['doesntwork']} <br>";
			$template->pageData['mainBody'] .= "<b>Requirements</b>: {$t['doesntworkunless']} <br>";
			$template->pageData['mainBody'] .= "<b>Workedif</b>: {$t['workedif']} <br>";
			$template->pageData['mainBody'] .= "<b>Variations</b>: {$t['variations']} <br>";
			$template->pageData['mainBody'] .= "<b>Solution statement</b>: {$t['solutionstatement']} <br>";
		    }
	    }
	    $template->pageData['mainBody'] .= "</ul>";
	    
	    
	// Comments
	
	$query = "SELECT * FROM user_comments_teachingpractice WHERE teachingpractice_id = '$tp_id';";		
	$comments =  dataConnection::runQuery($query);

	if($comments != false)
	{
	   	 $template->pageData['mainBody'] .= "<h2>Comments:</h2><br>";
	    	 foreach($comments as $c)
		    {
			    $postuser = user::retrieve_user($c['user_id']);		
			    		    
		            $template->pageData['mainBody'] .= "<b>User</b>:";
			
					$d = dir("images");
						
					while (false !== ($entry = $d->read()))
						
					{
						
						  if (strpos($entry, $postuser->id) !== false)
						
						{
						
						  	   $template->pageData['mainBody'] .= "<br><img src='images/{$entry}' height='100' width='100'/>";
						
						}
						
					}
					
			$template->pageData['mainBody'] .= "<br> {$postuser->name} {$postuser->lastname} (<a href='/prototype_nb1/user_profile.php?profile={$postuser->id}'>{$postuser->username}</a>) <br><br>";
			$template->pageData['mainBody'] .= "<b>Time</b>: <br> {$c['time']} <br><br>";
			$template->pageData['mainBody'] .= "<b>Comment</b>: <br> {$c['comment']} <br><br>";
		    }
	}							
	    
	    
	$template->pageData['mainBody'] .= "<h2>Leave a comment:</h2>";

		
    	//Example of use of form comment

    	$exampleform = new add_comment();
    	switch($exampleform->getStatus())
    	{
    	case FORM_NOTSUBMITTED:
    	    //$exampleform->setData($existingdata);
		$exampleform->teachingpractice_id = $tp_id;
		
    	    $template->pageData['mainBody'] .= $exampleform->getHtml();
    	    break;
    	case FORM_SUBMITTED_INVALID:
    	    $template->pageData['mainBody'] .= $exampleform->getHtml();
    	    break;
    	case FORM_SUBMITTED_VALID:
    	    $data = new user_comments_teachingpractice();
    	    $exampleform->getData($data);
            $data->time = time();
            $data->user_id = $dbUser->id;
	    $data->teachingpractice_id = $tp_id;
            $data->insert();
	    
    	    dataConnection::runQuery("UPDATE user SET points_comments = (points_comments + $points_comments) WHERE id = $userID;");
	    header("Location:view_teaching_practice.php?sessionID=$tp_id");
    	    break;
    	case FORM_CANCELED:
    	    //header('Location:index.php');
    	    break;
    	}

    if($dbUser->isadmin){
        $template->pageData['mainBody'] .= "<form action='deletebundle.php' method='post'><input type='hidden' name='id' value='".$tp_id."'/><input type='submit' value='Delete'></form>";
    }


	    

    $template->pageData['sideInfo'] .= "<h2> Menu </h2> <br> <a href='/prototype_nb1/index.php'>Main Page</a> <br><br> <a href='/prototype_nb1/badges.php'>Badges</a>";

    $template->pageData['logoutLink'] = loginBox($uinfo);
}



//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";

?>