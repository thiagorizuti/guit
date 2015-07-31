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

$template->pageData['pagetitle'] = 'University of Glasgow GUIT';
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] .= "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";

$template->pageData['sideInfo'] .= "<h2> Menu </h2>";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/index.php'>Home</a> ";
$template->pageData['sideInfo']	.= "<a class='menuItem' href='/browse.php'>Browse Teaching Practices</a>";
$template->pageData['sideInfo']	.= "<a class='menuItem' href='/addbundle.php'>New Teaching Practice</a>";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/user_profile.php?profile=$userID'>My Profile</a> ";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/badges.php'>Badges</a>";
if ($dbUser->isadmin == true) {
	$template->pageData['sideInfo'] .= "<a href='/manageusers.php'>Manage Users</a>";
}

if($uinfo==false)
{
	header("Location: index.php");
	exit();
}

else {
	if (isset($_GET['tpID'])){
		$tpID = $_GET['tpID'];
	}if(isset($_POST['commentSubmit'])) {
		$content = $_POST['commentContent'];
		$time = date('Y-m-d H:i:s');
		dataConnection::runQuery("INSERT INTO user_comments_teachingpractice (user_id,
			teachingpractice_id, time, comment) VALUES ('$userID','$tpID','$time','$content') ");
	}if(isset($_POST['repliedID'])){
		$repliedID = $_POST['repliedID'];
		$content = $_POST['replyContent'.$repliedID];
		$time = date('Y-m-d H:i:s');
		dataConnection::runQuery("INSERT INTO user_comments_teachingpractice (user_id,
			teachingpractice_id, time, comment, reply) VALUES ('$userID','$tpID','$time','$content', '$repliedID') ");
	}if(isset($_POST['like'])){
		$authorID = $_POST['$auhtorID'];
		if($_POST['like'] == 'like'){
			$time = date('Y-m-d H:i:s');
			dataConnection::runQuery("INSERT INTO user_likes_teachingpractice (user_id, teachingpractice_id, time)
			VALUES ('$userID','$tpID','$time') ");

		}if($_POST['like'] == 'unlike'){
			dataConnection::runQuery("DELETE FROM user_likes_teachingpractice WHERE user_id = '$userID' AND teachingpractice_id = '$tpID'");
		}
	}



	// Teaching Practice

	$teachingPractice = dataConnection::runQuery("SELECT u.id, u.name, u.lastname, u.username, tp.* FROM user AS u
		INNER JOIN teachingpractice AS tp ON u.id = tp.author_id WHERE tp.id = '{$tpID}'");

	$template->pageData['mainBody'] .= "<h2>Teaching Practice</h2>";
	$template->pageData['mainBody'] .= "<div id='teachingPractice'>";
	foreach($teachingPractice as $tp) {

		$template->pageData['mainBody'] .= "<img src=".getAvatarURL($tp['author_id'])." />";
		$template->pageData['mainBody'] .= "<a href='/user_profile.php?profile={$tp['author_id']}'>{$tp['name']} {$tp['lastname']} ({$tp['username']})</a>";
		$template->pageData['mainBody'] .= "{$tp['time']}";
		$template->pageData['mainBody'] .= "<table>";
		$template->pageData['mainBody'] .= "<tr><td><h3>{$tp['title']}</h3></td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Problem addressed</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['problemstatement']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Description</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['thisbundle']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>How it works</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['wayitworks']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Ways to make it work better</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['worksbetter']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Things that stip it working</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['doesntwork']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Requirements</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['doesntworkunless']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Worked if</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['workedif']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Variations</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['variations']}</td></tr>";
		$template->pageData['mainBody'] .= "<tr class='alt'><td>Solution statement</td></tr>";
		$template->pageData['mainBody'] .= "<tr><td>{$tp['solutionstatement']}</td></tr>";
		$template->pageData['mainBody'] .= "</table>";
	}
	$template->pageData['mainBody'] .= "</div>";

	//Like Button
	$query = "SELECT * FROM user_likes_teachingpractice WHERE teachingpractice_id = '$tpID'";
	$likes =  dataConnection::runQuery($query);
	$count = 0;
	$liked = false;
	foreach($likes as $l){
		$count++;
		if ($l['user_id'] == $userID){
			$liked = true;
		}
	}
	if($liked == false){
		$template->pageData['mainBody'] .= "<form action='' method='post'><input type='image' src='img/like.png' name='like'value='like' style='heigth:50px;width:50px;' /></form>";
	}else{
		$template->pageData['mainBody'] .= "<form action='' method='post'><input type='image' src='img/liked.png' name='like'value='unlike' style='heigth:50px;width:50px;' /></form>";
		$template->pageData['mainBody'] .= "You liked this.";
	}
	$template->pageData['mainBody'] .= "<br>{$count} likes";



	//Comments
	$query = "SELECT * FROM user_comments_teachingpractice WHERE teachingpractice_id = '$tpID' AND reply IS NULL";
	$comments =  dataConnection::runQuery($query);

	if($comments != false) {
		$template->pageData['mainBody'] .= "<h2>Comments:</h2>";
		foreach ($comments as $c) {
			$template->pageData['mainBody'] .= "<div id='comment'>";

			$postuser = user::retrieve_user($c['user_id']);

			$template->pageData['mainBody'] .= "<img src=" . getAvatarURL($c['user_id']) . "  />";

			$template->pageData['mainBody'] .= "<a href='/prototype_nb1/user_profile.php?profile={$postuser->id}'>{$postuser->name} {$postuser->lastname}({$postuser->username})</a>";
			$template->pageData['mainBody'] .= "{$c['comment']}";
			$template->pageData['mainBody'] .= "{$c['time']} ";
			$template->pageData['mainBody'] .= "<button id='replyButton{$c['id']}'>Reply</button>";
			$template->pageData['mainBody'] .= "<div id='replyForm{$c['id']}' style='display:none'>";
			$template->pageData['mainBody'] .= "<form action='' method='post'>
													<textarea name='replyContent{$c['id']}'></textarea>
													<input type='hidden' name='repliedID' value='{$c['id']}'/>
													<input type='submit' name='replySubmit{$c['id']}' value='Comment'/>
												</form><button id='replyCancel{$c['id']}'>Cancel</button>";
			$template->pageData['mainBody'] .= "</div>";
			$template->pageData['mainBody'] .= "<script type='text/javascript'>
												function swapElements(show,hide) {
													document.getElementById(show).style.display = 'block';
													document.getElementById(hide).style.display = 'none';
												}
												document.getElementById('replyButton{$c['id']}').addEventListener('click',function(e){
													swapElements('replyForm{$c['id']}','replyButton{$c['id']}');
												});
												document.getElementById('replyCancel{$c['id']}').addEventListener('click',function(e){
													swapElements('replyButton{$c['id']}','replyForm{$c['id']}');
												});
           										</script>";
			$cID = $c['id'];
			$query = "SELECT * FROM user_comments_teachingpractice WHERE teachingpractice_id = '$tpID' AND reply = '$cID'";
			$replies = dataConnection::runQuery($query);

			foreach ($replies as $r) {
				$template->pageData['mainBody'] .= "<div id='reply'>";

				$postuser = user::retrieve_user($r['user_id']);

				$template->pageData['mainBody'] .= "<img src=" . getAvatarURL($r['user_id']) . "  />";

				$template->pageData['mainBody'] .= "<a href='/prototype_nb1/user_profile.php?profile={$postuser->id}'>{$postuser->name} {$postuser->lastname}({$postuser->username})</a>";
				$template->pageData['mainBody'] .= "{$r['comment']}";
				$template->pageData['mainBody'] .= "{$r['time']} ";
				$template->pageData['mainBody'] .= "</div>";
			}
		}
	}

	$template->pageData['mainBody'] .= "<h2>Leave a comment:</h2>";
	$template->pageData['mainBody'] .= "<div id='commentForm'>";
	$template->pageData['mainBody'] .= "<form action='' method='post'><textarea name='commentContent'></textarea><input type='hidden' name='tpID' value={$tpID}/><input type='submit' name='commentSubmit' value='Comment'/></form>" ;
	$template->pageData['mainBody'] .= "</div>";

	$template->pageData['mainBody'] .= "<h3>Administrator Options</h3>	";
	if($dbUser->isadmin){
		$template->pageData['mainBody'] .= "<form action='deletebundle.php' method='post'><input type='hidden' name='id' value='".$tpID."'/><input type='submit' value='Delete'></form>";
	}
}


	$template->pageData['logoutLink'] = loginBox($uinfo);

//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";

?>