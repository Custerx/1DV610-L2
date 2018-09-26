<?php
//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Session.php');
require_once('model/Auth.php');
require_once('controller/LoginController.php');

require_once('view/RegisterView.php');
require_once('model/RegisterModel.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//CREATE OBJECTS OF THE CONTROLLERS
$lc = new \Controller\LoginController();

$lc->routerHandler();
