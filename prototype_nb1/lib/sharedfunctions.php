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


