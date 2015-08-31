<?php

// Get the user object for the database user record of a logged in user,
// and create it if it doesn't exist.
function getUserRecord($uinfo)
{
    $dbUser = user::retrieve_by_username($uinfo['uname']);
    if($dbUser == false) // not found in database, so create a new record
    {
        $dbUser = new user();
        $dbUser->username = $uinfo['uname'];
        $dbUser->name = $uinfo['gn'];
        $dbUser->lastname = $uinfo['sn'];
        $dbUser->email = $uinfo['email'];
        $dbUser->joindate = time();
	$dbUser->points = 0; //new line
        $dbUser->insert();
    }
    $dbUser->lastaccess = time();
    $dbUser->update();
    return $dbUser;
}

function getAvatarURL($userID){
    if (file_exists("img/avatar_{$userID}.jpeg")){
        $avatar= "img/avatar_{$userID}.jpeg";
    }else if (file_exists("img/avatar_{$userID}.jpg")){
        $avatar= "img/avatar_{$userID}.jpg";
    }else if (file_exists("img/avatar_{$userID}.png")){
        $avatar= "img/avatar_{$userID}.png";
    }else{
        $avatar= "img/avatar_0.jpeg";
    }
    return $avatar;
}

function likePoints($userID)
{
    $result = dataConnection::runQuery("SELECT user_id FROM user_likes_teachingpractice WHERE user_id = '$userID' ");
    $count = 0;
    foreach ($result as $r) {
        $count++;
    }
    return $count;

}

function likedPoints($userID){
    $result =  dataConnection::runQuery("SELECT * FROM user_likes_teachingpractice, teachingpractice WHERE
      teachingpractice.author_id = '$userID' AND teachingpractice.id = user_likes_teachingpractice.teachingpractice_id");
    $count = 0;
    foreach($result as $r){
        $count++;
    }
    return $count*2;
}

function commentPoints($userID){
    $result =  dataConnection::runQuery("SELECT user_id FROM user_comments_teachingpractice WHERE user_id = '$userID' ");
    $count = 0;
    foreach($result as $r){
        $count++;
    }
    return $count*5;
}

function postPoints($userID){
    $result =  dataConnection::runQuery("SELECT author_id FROM teachingpractice WHERE author_id = '$userID' ");
    $count = 0;
    foreach($result as $r){
        $count++;
    }
    return $count*10;
}

function totalPoints($userID){
    return likePoints($userID) + likedPoints($userID) +  commentPoints($userID) + postPoints($userID);
}

function getBadgeURL($badgeID){
    return "img/badge_{$badgeID}.png";
}
function getBadges(){
    $query = "SELECT * FROM badge;";
    $badges =  dataConnection::runQuery($query);
    return $badges;
}

function getEarnedBadges($userID){
    $query = "SELECT * FROM badge, user_earns_badge, user WHERE user_earns_badge.user_id = user.id AND user.id = '$userID' AND badge.id = user_earns_badge.badge_id;";
    $badges =  dataConnection::runQuery($query);
    return $badges;
}

function userHasBadge($userID, $badgeID){
    $result =  dataConnection::runQuery("SELECT * FROM user_earns_badge WHERE user_id = '$userID' AND  badge_id = '$badgeID'");
    return $result;
}

function userEarnsBadge($userID,$badgeID){
    dataConnection::runQuery("INSERT INTO user_earns_badge (user_id, badge_id) VALUES('$userID','$badgeID')");
}

function getTeachingPractices(){
    $teachingPractices = dataConnection::runQuery("SELECT tp.time, tp.title, tp.id, tp.author_id, u.username, u.name,
      u.lastname FROM teachingpractice AS tp,user as u where tp.author_id = u.id ");
    return $teachingPractices;
}

function getTeachingPractice($tpID){
    $teachingPractice = dataConnection::runQuery("SELECT u.id, u.name, u.lastname, u.username, tp.* FROM user AS u
		INNER JOIN teachingpractice AS tp ON u.id = tp.author_id WHERE tp.id = $tpID");
    return $teachingPractice;
}

function getLatestTeachingPractices($limit){
    $query = "SELECT * FROM teachingpractice ORDER BY id DESC LIMIT $limit";
    $teachingPractices = dataConnection::runQuery($query);
    return $teachingPractices;
}

function deleteTeachingPractice($tpID){
    dataConnection::runQuery("DELETE FROM teachingpractice  WHERE id = '$tpID'");
}

function getUsers(){
    $query = "SELECT * FROM user";
    $users =  dataConnection::runQuery($query);
    return $users;
}

function changeUserType($id){
    $query = "UPDATE user SET isadmin = NOT isadmin WHERE id = '$id' ";
    dataConnection::runQuery($query);
}

function deleteUser($id){
    $query = "DELETE FROM user  WHERE id = '$id'";
    dataConnection::runQuery($query);
}

function getUserInfo($id){
    $user = dataConnection::runQuery("SELECT * FROM user WHERE id = '$id'");
    return $user;
}

function getLikes($tpID){
    $query = "SELECT * FROM user_likes_teachingpractice WHERE teachingpractice_id = '$tpID'";
    $likes =  dataConnection::runQuery($query);
    return $likes;
}

function getComments($tpID){
    $query = "SELECT * FROM user_comments_teachingpractice WHERE teachingpractice_id = '$tpID' AND reply IS NULL ORDER BY time";
    $comments =  dataConnection::runQuery($query);
    return $comments;
}

function getReplies($tpID, $commentID){
    $query = "SELECT * FROM user_comments_teachingpractice WHERE teachingpractice_id = '$tpID' AND reply = '$commentID'";
    $replies = dataConnection::runQuery($query);
    return $replies;
}

function setComment($userID,$tpID,$time,$content){
    dataConnection::runQuery("INSERT INTO user_comments_teachingpractice (user_id,
			teachingpractice_id, time, comment) VALUES ('$userID','$tpID','$time','$content') ");
}

function setReply($userID,$tpID,$time,$content,$repliedID){
    dataConnection::runQuery("INSERT INTO user_comments_teachingpractice (user_id,
			teachingpractice_id, time, comment, reply) VALUES ('$userID','$tpID','$time','$content', '$repliedID') ");
}

function setLike($userID,$tpID, $time){
    dataConnection::runQuery("INSERT INTO user_likes_teachingpractice (user_id, teachingpractice_id, time)
			VALUES ('$userID','$tpID','$time') ");
}

function deleteLike($userID,$tpID){
    dataConnection::runQuery("DELETE FROM user_likes_teachingpractice WHERE user_id = '$userID' AND teachingpractice_id = '$tpID'");

}



?>



