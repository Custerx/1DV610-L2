<?php
//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Session.php');
require_once('model/Auth.php');
require_once('controller/LoginController.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');
// CREATE OBJECT OF THE MODELS
$s = new \Model\Session();
$a = new \Model\Auth($s);
//CREATE OBJECTS OF THE CONTROLLERS
$lc = new \Controller\LoginController($a, $s);

$lc->doLogin();
