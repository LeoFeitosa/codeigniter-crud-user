<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = '';

/* Routes Users*/
$route['auth'] = 'user/user';
$route['logout'] = 'user/user/logout';
$route['addedit'] = 'user/user/add_edit';
$route['details/(:num)'] = 'user/user/details/$1';
$route['recover'] = 'user/user/recover';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
