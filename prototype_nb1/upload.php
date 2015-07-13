<html>

<head>
  <title>File upload demo</title>
</head>

<body>

<?php

require_once('config.php');
require_once('lib/database.php');
require_once('lib/sharedfunctions.php');
require_once('corelib/dataaccess.php');
require_once('file_upload_demo/lib/uploadForm.php');

$uinfo = checkLoggedInUser();
$dbUser = getUserRecord($uinfo);
$userID = $dbUser->id;


if($uinfo==false)
{
	header("Location: index.php");
	exit();
}
else
{



/* I've used  my form wizard at http://home.niallbarr.me.uk/wizards2/formwiz_v2/index.php

to create the uploadForm class. It fills a single field, thefile, with details about

an uploaded file.



#form uploadForm;

upload thefile "Select and upload a image file here";

okcancel 'Yes, do it' "No, cancel it";

*/







$exampleform = new uploadForm();

$output = '';
$temp = "no_image_found";

switch($exampleform->getStatus())

{

case FORM_NOTSUBMITTED:

    //$exampleform->setData($existingdata);

    $output = $exampleform->getHtml();

    break;

case FORM_SUBMITTED_INVALID:

    $output = $exampleform->getHtml();

    break;

case FORM_SUBMITTED_VALID:

    $data = new stdClass();

    $exampleform->getData($data);



    //If a file has been uploaded, information about it is held in an array

    // $exampleform->thefile, and now duplicated in $data->thefile

    // The array members are:

    // [name] => the original file name

    // [type] => the file mime-type, e.g. 'image/png'

    // [tmp_name] => a temporary storage location from where the file can be opened or copied

    // [error] => any errors that occured

    // [size] => The file size in bytes



    // In this example I want to be sure it's an image (gif, png or JPEG)

    // And the easiest way to do that is to open it (Checking the mime-type isn't

    // sufficent as an attacker could spoof that.)


    switch($data->thefile['type'])

    {
        case 'image/gif':

            // The @ before the function call suppresses error messages

            $im = @imagecreatefromgif($data->thefile['tmp_name']);

            $ext = '.gif';

            break;

        case 'image/jpeg':

            $im = @imagecreatefromjpeg($data->thefile['tmp_name']);

            $ext = '.jpeg';

            break;

        case 'image/png':

            $im = @imagecreatefrompng($data->thefile['tmp_name']);

            $ext = '.png';

            break;

        default:

            $im = false;

        	echo "<p>I don't know what to do with mime-type {$data->thefile['type']}</p>";

            break;

    }

    // If I was really doing this properly, for avitars, I'd probably now

    // copy the image at the correct size into a new image, and save that as a png,

    // however for this demonstration I'll just copy the original file.

    if($im)

    {

        // Make a new file name that I know isn't going to be a problem

	$temp = preg_replace('/[^\w]+/','_',$data->thefile['name']);
    	$fileOut = 'images/img_'.$temp.$ext;
	    

        // I should really check it's not a duplicate, but not for now.
	
	$file_name = 'images/img_' . $userID . $ext;

	echo $output;
	
        copy($data->thefile['tmp_name'], $file_name);

    }

    // For demo purposes, just allow another upload

    $exampleform = new uploadForm();

    $output = $exampleform->getHtml();
    
    header('Location:index.php');    

    break;

case FORM_CANCELED:

    header('Location:index.php');

    break;

}

echo $output . '<hr>';

}
?>



</body>

</html>
