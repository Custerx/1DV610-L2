<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Session.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');
// CREATE OBJECT OF THE MODELS
$s = new \Model\Session();
//CREATE OBJECTS OF THE VIEWS
$v = new LoginView($s);
$dtv = new DateTimeView();
$lv = new LayoutView();

// isLoggedIn boolean.
$isLoggedIn = $v->authentication();

$lv->render($isLoggedIn, $v, $dtv);

