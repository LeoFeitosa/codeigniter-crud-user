<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = '';

/* Routes Users*/
$route['auth'] = 'user/user';
$route['add'] = 'user/user/add';
$route['details/(:num)'] = 'user/user/details/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
