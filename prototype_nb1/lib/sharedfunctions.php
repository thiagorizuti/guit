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

?>



