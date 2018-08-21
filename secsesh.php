<?php
namespace secSesh;
/*
secSesh - PHP session handling
by @rahuldottech
v1.0
--
https://github.com/rahuldottech/
https://rahul.tech/
*/

//===CONFIGURATION===
// Name of the session id cookie
const sessionName = 'secID';
// How long should an "idle" session remain valid 
// for? (in seconds)
const sessionTimeout = 60*60*24;
// How long should the session cookie remain valid 
// for? (in seconds)
const cookieLifetime = 4*60*60*24;
// --Fingerprint Settings--
	// - Use fingerprints?
	const useFingerprint = true;
	// - Use user agent in fingerprint?
	const f_useUserAgent = true;
	// - Use IP address in fingerprint?
	const f_useIPaddress = true;
//===END CONFIGURATION===

//PHP ini settings
ini_set( 'session.use_only_cookies', TRUE );				
ini_set( 'session.use_trans_sid', FALSE );
ini_set( 'session.cookie_httponly', TRUE );
ini_set( 'session.gc_maxlifetime', cookieLifetime );
ini_set( 'session.cookie_lifetime', cookieLifetime );
ini_set( 'session.name', sessionName );
session_name(sessionName);

function s_end(){
	if($_SESSION["s_loggedIn"]){
		$_SESSION["s_loggedIn"]=false;
		session_destroy();
	}
}

function s_start(){
	if(!$_SESSION["s_loggedIn"]){
		session_regenerate_id(true);
		$_SESSION["s_loggedIn"] = true;
		$_SESSION["s_lastActivity"] = time();
		if(useFingerprint){
			$_SESSION["fingerprint"] = generateFingerprint();
		}
	}
}

function generateFingerprint(){
	$fingerprint = "";
	if(f_useUserAgent){
		$fingerprint .= $_SERVER['HTTP_USER_AGENT'];
	}
	$fingerprint .= '_._'; //separator
	if(f_useIPaddress){
		$fingerprint .= $_SERVER['REMOTE_ADDR'];
	}
	$fingerprint = md5($fingerprint);
	return $fingerprint;
}

function s_check(){
	if($_SESSION["s_loggedIn"]){
		
		if(useFingerprint && generateFingerprint() !== $_SESSION["fingerprint"]){
			return false;
		}
		
		if((time() - $_SESSION['s_lastActivity']) > sessionTimeout){
			$_SESSION["s_loggedIn"]=false;
			session_destroy();
			return false;
		} else {
			$_SESSION['s_lastActivity'] = time();
			return true;	
		}
		
	} else {
		return false;
	}
}
?>
