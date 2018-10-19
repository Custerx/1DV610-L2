<?php
//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Session.php');
require_once('controller/MasterController.php');
require_once('model/DatabaseModel.php');
require_once('view/RegisterView.php');
require_once('model/RegisterModel.php');
require_once('model/Member.php');
require_once('test/CreateTestMember.php');
require_once('view/AccountView.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//CREATE OBJECT OF THE CONTROLLER
$mc = new \Controller\MasterController();

$mc->routerHandler();
