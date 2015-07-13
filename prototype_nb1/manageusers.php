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
$template->pageData['breadcrumb'] .= '| <a href="index.php">GUIT</a>';

if($uinfo==false || $dbUser->isadmin == false)
{
    header("Location: index.php");
    exit();
}

else
{
    $template->pageData['mainBody'] .= "<h2>List Of Users</h2>";

    $query = "SELECT * FROM user";
    $users =  dataConnection::runQuery($query);

    if($users != false)
    {

        $template->pageData['mainBody'] .= "<table><tr><td>Username</td><td>Last name</td><td>First name</td>
            <td>Email</td><td>Join date</td><td>Last access</td><td>Privileges</td><td>Options</td></tr>";
        foreach($users as $u)
        {
            if($u['id'] != $userID) {
                if ($u['isadmin'] == true) {
                    $type = "Administrator";
                } else {
                    $type = "Standard";
                }
                $template->pageData['mainBody'] .= "<tr><td>{$u['username']}</td><td>{$u['lastname']}</td>
                <td>{$u['name']}</td><td>{$u['email']}</td><td>{$u['joindate']}</td><td>{$u['lastaccess']}</td>
                <td>{$type}</td><td><form action='' method='post'><input type='hidden' name='id' value={$u['id']} />
                <input type='submit' value='Change privileges' name='change'><br><input type='submit' value='Delete' name='delete'>
                </form></td></tr>";
            }

        }
        $template->pageData['mainBody'] .= "</table>";

    }

    if (isset($_POST['change'])) {
        $id = $_POST['id'];
        $query = "UPDATE user SET isadmin = NOT isadmin WHERE id = '$id' ";
        dataConnection::runQuery($query);
        header("Location: manageusers.php");
        exit();
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $query = "DELETE FROM user  WHERE id = '$id'";
        dataConnection::runQuery($query);
        header("Location: manageusers.php");
        exit();
    }


    $template->pageData['sideInfo'] = "<h2> Menu </h2> <br> <a href='/prototype_nb1/index.php'>Main Page</a>";

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