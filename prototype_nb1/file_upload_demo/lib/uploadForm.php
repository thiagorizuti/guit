<?php
require_once('corelib/form_lib2.php');

//Original source useed with http://home.niallbarr.me.uk/wizards2/formwiz_v2/index.php
/*
#form uploadForm;
upload thefile "Select and upload a image file here";
okcancel 'Yes, do it' "No, cancel it";
*/


class uploadForm extends nbform
{
	var $form_magic_id = 'e2381af677032774746fab4ae6eb3059';
	var $thefile; //upload
	var $validateMessages;

	function __construct($readform=true)
	{
		parent::__construct();
		$this->validateMessages = array();
		if($readform)
		{
			$this->readAndValidate();
		}
	}

	function setData($data)
	{
		$this->thefile = $data->thefile;
	}

	function getData(&$data)
	{
		$data->thefile = $this->thefile;
		return $data;
	}

	function readAndValidate()
	{
		$isCanceled=false;
		if((isset($_REQUEST['uploadForm_code']))&&($_REQUEST['uploadForm_code'] == $this->form_magic_id))
		{
			$this->thefile = $_FILES['thefile'];
			if("No, cancel it" == $_REQUEST['submit'])
				$isCanceled = true;
			$isValid = $this->validate();
			if($isCanceled)
				$this->formStatus = FORM_CANCELED;
			elseif($isValid)
				$this->formStatus = FORM_SUBMITTED_VALID;
			else
				$this->formStatus = FORM_SUBMITTED_INVALID;
		}
		else
			$this->formStatus = FORM_NOTSUBMITTED;
	}

	function validate()
	{
		$this->validateMessages = array();
		// Put custom code to validate $this->thefile here. Put error message in $this->validateMessages['thefile']
		if(sizeof($this->validateMessages)==0)
			return true;
		else
			return false;
	}

	function getHtml()
	{
		$out = '';
		$out .= $this->formStart(false, 'POST', 'multipart/form-data');
		$out .= $this->hiddenInput('uploadForm_code', $this->form_magic_id);
		$out .= $this->uploadInput('Select and upload a image file here (100x100)', 'thefile', $this->validateMessages);
		$out .= $this->submitInput('submit', 'Upload', "Cancel");
		$out .= $this->formEnd(false);
		return $out;
	}

	function post_it()
	{
	    $http = new Http();
	    $http->useCurl(false);
	    $formdata=array('thanks_url'=>'none', 'mymode'=>'webform1.0', 'datafile'=>'uploadForm', 'coderef'=>'nsb2x');
	    $formdata['thefile'] = $this->thefile;

	    $http->execute('http://culrain.cent.gla.ac.uk/cgi-bin/qh/qhc','','POST',$formdata);
	    return ($http->error) ? $http->error : $http->result;
	}

}

?>
