<?php
/**
 * SanitizeUploadFile
 * @version : 0.0.1
 * @author Yeremi Loli (yeremiloli@yahoo.com)
 * 
 * Sanitize & Upload Files
 * Created on 06/10/2011
 *
 * 	Sanitize & Upload Files.
 * 	- Replace many accented letters to clean letters.
 *	- Give a prefix using microtime(true). More explanation in http://php.net/manual/en/function.microtime.php
 *	- Give a unique name using unique() php function. More explanation in http://php.net/manual/en/function.uniqid.php
 *  - Joins the new name and new extension.
 *  - Upload files. Multi or a single file. 
 *	- Verify allowed extensions. 
 * 
	HOW TO
	------
	if($_POST){
		$instance = new SanitizeUploadFile($_FILES['filename']);
		$instance->renameFile = true;
		
		Options
		$instance->upload = true;
		$instance->upload_dir = 'uploads/';
		
		Optionals
		$instance->prefixSuggest = 'yeremi_';
		$instance->allowedExt = 'jpg|png';
	}	
 **/

class SanitizeUploadFile {
	
	/**
	 * $renameFile	Set if the file will be renamed
     * @access public
	 */
	var $renameFile		= true;
	
	/**
	 * Optional
	 * $prefixSuggest	Set the prefix name for the file
	 * @access public
     */
	var $prefixSuggest	= '';
	
	/**
	 * $upload_dir	Set the directory to upload file destinations.
	 * If $upload == true you need to especify this variable.
	 * @access public
     */
	var $upload_dir		= '';
	
	/**
	 * $allowedExt	If empty we will use a long extensions list, otherwise we use your allow extensions especifics
	 * @access public
     */
	var $allowedExt		= ""; 
	
	/**
	 * function __construct()
	 * @access private
	 * Not Allowed
	 * @return
	
	private function __construct(){
	} */
	
	/**
	 * function init()
	 * @access private
	 * @param NULL Get the global file name
	 * @internal If $this->upload == TRUE get the prefix suggested by user, 
	 *	otherwise crate a prefix by microtime function.
	 * @return functions messages
	 */
	public function init($arrayfile) {
		if( $this->upload_dir != '' ) {
			$this->uploadAttachedFile($arrayfile);
		} else {
			echo 'Without directory to upload?.';
		}
	}
	
	/**
	 * function sanitize()
	 * @access private
	 * @param NULL Get the global file name.
	 * @internal If $this->prefixSuggest != NULL get the prefix suggested by user, 
	 *	otherwise crate a prefix by microtime machine.
	 * @internal If $this->renameFile == true return the user file name suggested, 
	 *	otherwives return the original but sanitize file name. 
	 * @return string
	 */
	private function sanitize($param) {
		
		$filename	= pathinfo($param, PATHINFO_FILENAME);
		$ext		= pathinfo($param, PATHINFO_EXTENSION);
		
		$fileSanitized = strtr( $filename,"ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ","SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
		$fileSanitized = str_replace(" ", "", $fileSanitized); 
		$fileSanitized = str_replace("#", "no", $fileSanitized); 
		$fileSanitized = str_replace("$", "dollar", $fileSanitized); 
		$fileSanitized = str_replace("%", "percent", $fileSanitized); 
		$fileSanitized = str_replace("^", "", $fileSanitized); 
		$fileSanitized = str_replace("&", "and", $fileSanitized); 
		$fileSanitized = str_replace("*", "", $fileSanitized); 
		$fileSanitized = str_replace("?", "", $fileSanitized); 	
		
		if($this->renameFile == true){				
			$prefix = ($this->prefixSuggest != '') ? 
				$this->prefixSuggest : 
					$prefix = microtime(true);
			$name = uniqid($prefix, true);
		} else {
			$name = $fileSanitized;
		}
		return $name.'.'.$ext;
	}
	
	/**
	 * function uploadAttachedFile()
	 * @access private
	 * @param $FILES The file(s) name.
	 * @internal If $this->allowedExt == NULL verify the file extensions with the most common extension:
	 *	txt|csv|htm|html|xml|css|doc|xls|rtf|ppt|pdf|swf|flv|avi|wmv|mov|jpg|jpeg|gif|png
	 *	otherwise verify with the user list extensions.
	 * @return string messages
	 */
	private function uploadAttachedFile($FILES) {
		
		$_ERROR_[0] = "Unknown problem with upload.";
		$_ERROR_[1] = "Uploaded file too large (load_max_filesize).";
		$_ERROR_[2] = "Uploaded file too large (MAX_FILE_SIZE).";
		$_ERROR_[3] = "File was only partially uploaded.";
		$_ERROR_[4] = "Choose a file to upload.";
		
		$nFiles = count($FILES['name']);
		
		$IMG_TEMP = $FILES['tmp_name'];
		$IMG_NAME = $FILES['name'];
		$IMG_ERRO = $FILES['error'];
		
		if($this->allowedExt == ''){
			$exreg = "txt|htm|html|xml|csv|css|doc|xls|rtf|ppt|pdf|swf|flv|avi|wmv|mov|jpg|jpeg|gif|png";
		} else {
			$exreg = $this->allowedExt;
		}
		
		$out	= '';
		$j		= 1;
		$total	= '';
		$completeImages = '';
		
		for ($i=0; $i < $nFiles; $i++) {		
			$img = $this->sanitize($IMG_NAME[$i]);
			$upload_file = $this->upload_dir.$img;
			if (!preg_match("/(".$exreg.")$/",$img)) {
				$total =  "<span style='color:red'>&#8226; This file is not allowed, sorry so much: <strong>".$IMG_NAME[$i].'</strong></span><br />';
			} else {
				if (is_uploaded_file($IMG_TEMP[$i])) {
					if (move_uploaded_file($IMG_TEMP[$i], $upload_file)) {
						//$total =  $j++.' Upload completed. <br /><br />';
						$completeImages .=  "<span style='color:green'>&#8226; ".$img."</span><br />";
					} else {
						$total =  $_ERROR_[$IMG_ERRO[$i]];
					}
				} else {
					$total =  $_ERROR_[$IMG_ERRO[$i]];
				}    
			}
		}
		echo $total.$completeImages.'<br />';
	}
	
}

?>