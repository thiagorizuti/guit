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
	if (isset($_GET['tpID']) && is_numeric($_GET['tpID']) && $_GET['tpID'] >= 0){
		$tpID = $_GET['tpID'];
	}else{
		header("Location: index.php");
		exit();
	}
	if(isset($_POST['commentSubmit'])) {
		setComment($userID,$tpID, date('Y-m-d H:i:s'), $_POST['commentContent']);
	}
	if(isset($_POST['repliedID'])){
		setReply($userID,$tpID,date('Y-m-d H:i:s'),$_POST['replyContent'.$repliedID],$_POST['repliedID']);
	}
	if(isset($_POST['like'])){
		$authorID = $_POST['$auhtorID'];
		if($_POST['like'] == 'like'){
			setLike($userID,$tpID, date('Y-m-d H:i:s'));
		}if($_POST['like'] == 'unlike'){
			deleteLike($userID,$tpID);
		}
	}



	// Teaching Practice

	$teachingPractice = getTeachingPractice($tpID);

	$template->pageData['mainBody'] .= "<h2>Teaching Practice</h2>";
	$template->pageData['mainBody'] .= "<div id='teachingPractice'>";
	foreach($teachingPractice as $tp) {

		$template->pageData['mainBody'] .= "<img src=".getAvatarURL($tp['author_id'])." />";
		$template->pageData['mainBody'] .= "<a href='/user_profile.php?profile={$tp['author_id']}'>{$tp['name']} {$tp['lastname']} ({$tp['username']})</a>";
		$template->pageData['mainBody'] .= "{$tp['time']}";
		$template->pageData['mainBody'] .= "<table>";
		$template->pageData['mainBody'] .= "<tr><td><h3>{$tp['title']}</h3></td></tr>";
		if($tp['problemstatement']!= false){
			$template->pageData['mainBody'] .= "<tr class='alt'><td>Keywords:</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['problemstatement']}</td></tr>";
		}
		if($tp['wayitworks']!= false) {
			$template->pageData['mainBody'] .= "<tr class='alt'><td>This bundle: </td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['wayitworks']}</td></tr>";
		}
		if($tp['worksbetter']!= false) {
			$template->pageData['mainBody'] .= "<tr class='alt'><td>What we did:</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['worksbetter']}</td></tr>";
		}
		if($tp['doesntwork']!= false) {
			$template->pageData['mainBody'] .= "<tr class='alt'><td>This only works if...</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['doesntwork']}</td></tr>";
		}
		if($tp['doesntworkunless']!= false) {
			$template->pageData['mainBody'] .= "<tr class='alt'><td>This works better if...</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['doesntworkunless']}</td></tr>";
		}
		if($tp['workedif']!= false) {
			$template->pageData['mainBody'] .= "<tr class='alt'><td>This works best if...</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['workedif']}</td></tr>";
		}
		if($tp['variations']!= false) {
			$template->pageData['mainBody'] .= "<tr class='alt'><td>This doesn’t work unless...</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['variations']}</td></tr>";
		}
		if($tp['solutionstatement']!= false) {
			$template->pageData['mainBody'] .= "<tr class='alt'><td>So:</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>{$tp['solutionstatement']}</td></tr>";
		}
		$template->pageData['mainBody'] .= "</table>";
	}
	$template->pageData['mainBody'] .= "</div>";

	//Like Button
	$likes =  getLikes($tpID);
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
	$comments =  getComments($tpID);

	if($comments != false) {
		$template->pageData['mainBody'] .= "<h2>Comments:</h2>";
		foreach ($comments as $c) {
			$template->pageData['mainBody'] .= "<div id='comment'>";

			$postuser = user::retrieve_user($c['user_id']);

			$template->pageData['mainBody'] .= "<img src=" . getAvatarURL($c['user_id']) . "  />";

			$template->pageData['mainBody'] .= "<a href='user_profile.php?profile={$postuser->id}'>{$postuser->name} {$postuser->lastname}({$postuser->username})</a>";
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
			$commentID = $c['id'];
			$replies = getReplies($tpID,$commentID);

			foreach ($replies as $r) {
				$template->pageData['mainBody'] .= "<div id='reply'>";

				$postuser = user::retrieve_user($r['user_id']);

				$template->pageData['mainBody'] .= "<img src=" . getAvatarURL($r['user_id']) . "  />";

				$template->pageData['mainBody'] .= "<a href='user_profile.php?profile={$postuser->id}'>{$postuser->name} {$postuser->lastname}({$postuser->username})</a>";
				$template->pageData['mainBody'] .= "{$r['comment']}";
				$template->pageData['mainBody'] .= "{$r['time']} ";
				$template->pageData['mainBody'] .= "</div>";
			}
			$template->pageData['mainBody'] .= "</div>";
		}
	}

	$template->pageData['mainBody'] .= "<h2>Leave a comment:</h2>";
	$template->pageData['mainBody'] .= "<div id='commentForm'>";
	$template->pageData['mainBody'] .= "<form action='' method='post'><textarea name='commentContent'></textarea><input type='hidden' name='tpID' value={$tpID}/><input type='submit' name='commentSubmit' value='Comment'/></form>" ;
	$template->pageData['mainBody'] .= "</div>";



	if($dbUser->isadmin){
		$template->pageData['mainBody'] .= "<h3>Administrator Options</h3>	";
		$template->pageData['mainBody'] .= "<form action='deletebundle.php' method='post'><input type='hidden' name='id' value='".$tpID."'/><input type='submit' value='Delete'></form>";
	}
}


	$template->pageData['logoutLink'] = loginBox($uinfo);

//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";

?>