<?php
$userID = $_POST['userID'];
if($_FILES['avatar']['name']) {
    if(!$_FILES['avatar']['error']) {
        $valid_file = true;
        if($_FILES['avatar']['size'] > (1024000)){
            $valid_file = false;
            $message = 'The file\'s has to be less than 1 MB.';
        }
        if($valid_file) {

            $fileName = $_FILES['avatar']['name'];
            echo $fileName;
            echo "<br/>";
            $extension = strrchr ($fileName, ".");
            echo $extension."\n";
            echo "<br/>";
            $fileName = "avatar_".$userID.$extension;
            echo $fileName."\n";
            echo "<br/>";
            move_uploaded_file($_FILES['avatar']['tmp_name'], 'img/'.$fileName);
            $message = 'Congratulations!  Your file was accepted.';
        }
    }
    else {
        $message = 'Ooops! Something went wrong:  '.$_FILES['photo']['error'];
    }

}

header("Location: user_profile.php?profile=$userID");
exit();
echo $message;

?>