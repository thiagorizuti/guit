<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');
require_once('lib/database.php');
require_once('lib/sharedfunctions.php');
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
    $template->pageData['sideInfo'] .= "<a href='/manageusers.php'>Manage Users</a>";
}

if($uinfo==false || $dbUser->isadmin == false)
{
    header("Location: index.php");
    exit();
}

else
{
    $users =  getUsers();

    $template->pageData['mainBody'] .= "<h2>List Of Users</h2>";
    $template->pageData['mainBody'] .= "<div id='manageUsers'>";
    $template->pageData['mainBody'] .= "<input class='search' placeholder='Search' />";
    $template->pageData['mainBody'] .= "<table>";
    $template->pageData['mainBody'] .= "<thead>";
    $template->pageData['mainBody'] .= "<th class='sort' data-sort='username'>Username</th>";
    $template->pageData['mainBody'] .= "<th class='sort'data-sort='lastname'>Last name</th>";
    $template->pageData['mainBody'] .= "<th class='sort' data-sort='firstname'>First name</th>";
    $template->pageData['mainBody'] .= "<th class='sort' data-sort='email'>Email</th>";
    $template->pageData['mainBody'] .= "<th class='sort' data-sort='joindate'>Join date</th>";
    $template->pageData['mainBody'] .= "<th class='sort' data-sort='lastaccess'>Last access</th>";
    $template->pageData['mainBody'] .= "<th class='sort' data-sort='privileges'>Privileges</th>";
    $template->pageData['mainBody'] .= "<th>Options</th>";
    $template->pageData['mainBody'] .= "</thead>";
    $template->pageData['mainBody'] .= "<tbody class='list'>";

    foreach($users as $u) {
        if($u['id'] != $userID) {
            if ($u['isadmin'] == true) {
                $privilege = "Administrator";
            } else {
                $privilege = "Standard";
            }
            $template->pageData['mainBody'] .= "<tr><td class='username'><a href='/user_profile.php?username={$u['username']}&profile={$u['id']}'>{$u['username']}</a></td>
            <td class='lastname'>{$u['lastname']}</td>
            <td class='name'>{$u['name']}</td>
            <td class='email'>{$u['email']}</td>
            <td class='joindate'>".date("d/m/Y",strtotime($u["joindate"]))."</td>
            <td class='lastaccess'>".date("d/m/Y",strtotime($u["lastaccess"]))."</td>
            <td class='privileges'>{$privilege}</td>
            <td><form action='' method='post'><input type='hidden' name='id' value={$u['id']} />
                <input type='submit' value='Change privileges' name='change'><br><input type='submit' value='Delete' name='delete'>
            </form></td></tr>";
        }

    }
    $template->pageData['mainBody'] .= "</tbody>";
    $template->pageData['mainBody'] .= "</table>";
    $template->pageData['mainBody'] .= "</div>";
    $template->pageData['mainBody'] .= "<script src='/js/list.min.js'></script>";
    $template->pageData['mainBody'] .= "<script type='text/javascript'>var options = {valueNames: [ 'username','lastname','firstname', 'email', 'joindate','lastaccess','privileges' ]};
        var userList = new List('manageUsers', options);</script>";

    if (isset($_POST['change'])) {
        $id = $_POST['id'];
        changeUserType($id);
        header("Location: manageusers.php");
        exit();
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        deleteUser($id);
        header("Location: manageusers.php");
        exit();
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