<?php
/*
Plugin Name: Auto Login
Description: This plugin is installed by CloudAccess.net in each WordPress site that is provisioned on the company's platform. It exists so that you can have one-click WordPress Dashboard access directly through your Cloud Control Panel (CCP). <a href='http://www.cloudaccess.net/products/cloud-control-panel.html' target='_blank'>Learn more about the CCP.</a>
Version: 1.0
Author: <a href='http://www.cloudaccess.net/' target='_blank'>CloudAccess.net</a>
*/

if (basename($_SERVER['PHP_SELF']) == basename (__FILE__)) {
    die('Sorry, but you cannot access this page directly.');
}

// function to auto login into admin
function ca_auto_login() {

    // only check token if user are not logged in wp-admin
    if ( is_admin() && (isset($_GET['catoken']) && $_GET['catoken'] != '') && intval($_GET['ccp']) && !is_user_logged_in() )
    {
	// alphanum filter
	$token = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['catoken']);

	$sso_link = get_option('ca_sso_link', 'https://sso.cloudaccess.net/');

	// use curl for request
	$response = new stdclass;
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $sso_link);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_POST, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, 'token='.$token);
	$response->body = curl_exec($ch);
	$response->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($response->code != 200) {
	    return false;
	} else {
	    if (empty($response->body)) {
		return false;
	    } else {
		$credentials = json_decode($response->body, true);

		if (!$credentials['success']) {
		    return false;
		} else {
		    $creds = array();
		    $creds['user_login'] = $credentials['data']['username'];
		    $creds['user_password'] = addslashes($credentials['data']['password']);
		    $creds['remember'] = false;

		    wp_clear_auth_cookie();
		    $secure_cookie = '';
		    if ( $user = get_user_by('login', $creds['user_login']) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
			    $secure_cookie = true;
			    force_ssl_admin(true);
			}
		    }
		    add_filter( 'wordfence_ls_require_captcha', '__return_false' );
		    $user = wp_signon( $creds, $secure_cookie );
		    if( is_wp_error( $user ) )
		    {
			$error = $user->get_error_message();
		    }
		    else
		    {
			$userID = $user->ID;
			wp_set_current_user( $userID );
			wp_set_auth_cookie($userID);

			if ( is_user_logged_in() )
			{
			    wp_redirect( home_url('wp-admin/') );exit();//dont remove this exit, else admin will not be logged in
			}
		    }
		}
	    }
	}
    }
}

add_action( 'after_setup_theme', 'ca_auto_login', 1 );