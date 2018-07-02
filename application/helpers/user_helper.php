
<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
 
if(!function_exists('check_login')) {
	function is_logged(){
		$CI = & get_instance();
		return ((empty($CI->session->userdata('user'))) ? false : true);
	}
}

if(!function_exists('data_user')) {
	function data_user(){
		$CI = & get_instance();
		return ((empty($CI->session->userdata('user'))) ? false : (array)$CI->session->userdata('user')[0]);
	}
}
