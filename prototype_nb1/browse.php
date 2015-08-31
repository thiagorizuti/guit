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

    $teachingPractice = getTeachingPractices();

    $template->pageData['mainBody'] .= "<h2>Browse Teaching Practices</h2>";
    $template->pageData['mainBody'] .= "<div id='browse'>";
    $template->pageData['mainBody'] .= "<input class='search' placeholder='Search' />";
    $template->pageData['mainBody'] .= "<button class='sort' data-sort='date'>Sort by date</button>";
    $template->pageData['mainBody'] .= "<button class='sort' data-sort='title'>Sort by title</button>";
    $template->pageData['mainBody'] .= "<button class='sort' data-sort='author'>Sort by author</button>";
    $template->pageData['mainBody'] .= "<table>";
    $template->pageData['mainBody'] .= "<thead>";
    $template->pageData['mainBody'] .= "<th class='sort' data-sort='date'>Date Created</th>";
    $template->pageData['mainBody'] .= "<th class='sort'data-sort='title'>Title</th>";
    $template->pageData['mainBody'] .= " <th class='sort' data-sort='author'>Author</th>";
    $template->pageData['mainBody'] .= "</thead>";
    $template->pageData['mainBody'] .= "<tbody class='list'>";

    foreach($teachingPractice as $tp){
            $template->pageData['mainBody'] .= "<tr><td class='date'>".date("d/m/Y",strtotime($tp["time"]))."</td>
            <td class='title'><a href='view_teaching_practice.php?title={$tp['title']}&tpID={$tp['id']}'>{$tp['title']}</a></td>
            <td class='author'><a href='/user_profile.php?username={$tp['username']}&profile={$tp['author_id']}'>{$tp['name']} {$tp['lastname']} ({$tp['username']})</a></td></tr>";
    }
    $template->pageData['mainBody'] .= "</tbody>";
    $template->pageData['mainBody'] .= "</table>";
    $template->pageData['mainBody'] .= "</div>";
    $template->pageData['mainBody'] .= "<script src='/js/list.min.js'></script>";
    $template->pageData['mainBody'] .= "<script type='text/javascript'>var options = {valueNames: [ 'date', 'title','author' ]};
            var userList = new List('browse', options);</script>";



    $template->pageData['logoutLink'] = loginBox($uinfo);

    echo $template->render();

}
?>
