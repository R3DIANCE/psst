<?php
/*
Psst File Sharer by rahuldottech
v3.1
--
Chuck this script up on a server, configure
options bellow, and lo and behold, you have 
your own file sharing system!
--
Requires secSesh.php for secure sessions.
https://github.com/rahuldottech/secSesh
--
No SQL or databases required!
--
https://rahul.tech/
github: @rahuldottech
twitter: @rahuldottech

Have fun!
*/

//===PREFRENCES ===

//Upload directory, INCLUDE TRAILING SLASH
const path = 'files/'; 

//Public URL to this folder, include HTTP(S) and trailing slash
$url = 'https://rahul.party/';

//sha-256 hash of password
//Default is 'password123' 
//PLEASE CHANGE, USE SECURE PASSWORD!
$password = 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f';

//Impose file size limit?
$imposeFileSize = false;
$max_file_size = 1024*1000; //1024*x = x kilobytes

//Impose file extension allowances?
$imposeFileAllowances = false;
$valid_formats = array("jpeg", "html", "txt", "rar", "7z", "jpg", "pdf", "doc", "docx", "png", "gif", "zip", "bmp");

//Impose file extension exclusions?
$imposeFileExclusions = false;
$invalid_formats = array("php", "exe", "cmd", "vbs", "bat");

//Enforce HTTPS?
$enforceHTTPS = true;

//===END PREFRENCES===

enforcessl();
require 'secsesh.php';
session_start();

if($_GET["logout"]){
	\secSesh\end();
	header('Location: '. $_SERVER['SCRIPT_NAME']);

}
if($_GET["delfile"] && isset($_POST["filename"])){
	if(\secSesh\check()){
		fileDelete();
	}
}

if(!\secSesh\check()){
	if(isset($_POST["password"])){
		if(hash('sha256', $_POST['password'])===$password){
				\secSesh\start();
		} else{
			echo "Incorrect password! <hr>";
		}	
	}
}

$count = 0; //Multiple File Upload Count (For Debugging)

if(\secSesh\check() && isset($_POST) && isset($_FILES) && $_SERVER['REQUEST_METHOD'] == "POST"){
	
    $c = 0;	
	// Loop $_FILES to execute all files
	foreach ($_FILES['files']['name'] as $f => $name) {   
        $c++;
		if($c==1){
		    	echo "<h3> Results: </h3>";
		}
		//sanitation
		$oname2 = $name;
		$oname = pathinfo($name, PATHINFO_FILENAME);
		$name = htmlspecialchars($name);
		$oname = str_replace(array('.', ',', '\\', '/', '<', '>', ' '), '' , $oname);	
		$oname = preg_replace('/\s+/', '', $oname);
		if ($_FILES['files']['error'][$f] == 4) {
	        continue; // Skip file if any error found
	    }	       
	    if ($_FILES['files']['error'][$f] == 0) {	           
	        if ($imposeFileSize == TRUE && $_FILES['files']['size'][$f] > $max_file_size) {
					echo "ERROR: \"" . $name . "\"" ." is too large!<br>";
	            continue; // Skip large files
	        }
			elseif($imposeFileAllowances == TRUE && !in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
					echo "ERROR: \"" . $name . "\"" ." has an invalid format!<br>";

				continue; // Skip invalid file formats
			}
			elseif($imposeFileExclusions == TRUE && in_array(pathinfo($name, PATHINFO_EXTENSION), $invalid_formats) ){
					echo "ERROR: \"" . $name . "\"" ." has an invaid format!<br>";
				continue; // Skip invalid file formats
			}
	        else{	 // No error found! Move uploaded files 
	            if(move_uploaded_file($_FILES["files"]["tmp_name"][$f], path.$name)){
					
				//rename file with UniqueID
					$newname = $oname.'-'.mt_rand(100, 9999).'.'.pathinfo($name, PATHINFO_EXTENSION);
					while(file_exists(path.$newname)){
						$newname = $oname.'-'.mt_rand(100, 9999).'.'.pathinfo($name, PATHINFO_EXTENSION);;
					}
					rename(path.$oname2, path.$newname);
	            $count++; // Number of successfully uploaded files
				echo "\"" . $name . "\"" ." has been successfully uploaded!<br>";
				echo "New file location: <a href=\"" . $url.path.$newname . "\">" . $url.path.$newname . '</a><br>';
				} else {
					echo "ERROR: \"" . $name . "\"" ." could not be uploaded!<br>";

				}
				echo "<hr>";
	        }
	    }
	}
}

function enforcessl(){
	if($enforceHTTPS = TRUE){
		if($_SERVER["HTTPS"] != "on")
		{
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		}
	}
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Psst File Sharer</title>
  <style>
	body {
		font-family: monospace;
	}
  </style>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<meta name="robots" content="noindex,nofollow">

</head>
<body>
<h2>Psst File Sharer</h2>
<?php 
if($_GET["getlist"]){
	filePrint();
} else if($_GET["delfile"]){
	delPrint();
} else {
	if(\secSesh\check()){
		uploadPrint();
	} else{
		loginPrint();
	}
}
footerPrint();

function uploadPrint(){
	print '
	<h3>Upload Files</h3>
	  <form action="#" method="post" enctype="multipart/form-data">
	  	<input type="file" id="file" name="files[]" multiple="multiple" accept="*" />
	  <input type="submit" value="Upload!" />
	</form>
	';
}

function loginPrint(){
	print '	<h3>Login</h3>
	  <form action="#" method="post">
		Password: <input type="password" name="password"><br>
	  <input type="submit" value="Login!" />
	</form>
	';
}

function delPrint(){
	print '	<h3>Delete File</h3>
	  <form action="#" method="post">
		Filename: <input type="text" name="filename"><br>
	  <input type="submit" value="Delete!" />
	</form>
	';
}

function footerPrint(){
$sep = '</b> | <b>';
$sep2 = '</b> | ';
print '<br><br><hr><div style ="0.5em"><b>';
	if(\secSesh\check()){
		if($_GET["getlist"] || $_GET["delfile"]){
			fprintHome();
			echo $sep;
		}
		if(!$_GET["getlist"]){
			fprintList();
		}
		if(!$_GET["getlist"] && !$_GET["delfile"]){
			echo $sep;
		}
		if(!$_GET["delfile"]){
			fprintDel();
		} 
		echo $sep;
		fprintLogout();
		echo $sep2;
	}
	print '<a href="https://github.com/rahuldottech/psst/">Help/Source</a></div>';
}

function fprintHome(){
	print '<a href="?">Home</a>';
}

function fprintLogout(){
	print '<a href="?logout=true">Logout</a>';
}
	
function fprintList(){
	print '<a href="?getlist=true">List files</a>';
}

function fprintDel(){
	print '<a href="?delfile=true">Delete file</a>';
}

function filePrint(){
	echo "<h3> File list: </h3>";

	$files = scandir(path);
	$filecount = 1;
	foreach($files as $file) {
		if($file != '.' and $file != '..') {
			echo $filecount . '] <a href="/' . path . $file . '">' . $file . "</a><br>";
			$filecount +=1;
		}
	}
}

function fileDelete(){
	if($_GET["delfile"]){
		echo "<h3> Results: </h3>";
		if (file_exists(path.$_POST["filename"])) {
			unlink(path.$_POST["filename"]);
			echo 'File deleted!<br>';
		} else {
			echo 'File doesn\'t exist!<br>';
		}
		echo "<hr>";
	}
}

?>
</body>
</html>
