<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
 
if(!function_exists('check_login')) {
	function is_logged(){
		$CI = & get_instance();

		$user_id = (!empty($CI->session->userdata('user')[0]->id) ? $CI->session->userdata('user')[0]->id : null);

		$CI->load->database();
		
		$session = $CI->db->select('session_id')
								->where('id', $user_id)
								->get('users')
								->row('session_id');

		$query = $CI->db->get_where('ci_sessions', array('id' => $session))->result();

		return ((empty($query)) ? false : true);
	}
}

if(!function_exists('data_user')) {
	function data_user(){
		$CI = & get_instance();
		return ((empty($CI->session->userdata('user'))) ? false : (array)$CI->session->userdata('user')[0]);
	}
}
