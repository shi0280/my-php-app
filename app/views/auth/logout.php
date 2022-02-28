<?php
require(dirname(__FILE__) . '/../../controllers/AuthController.php');
AuthController::logout();
header('location: login.php');
exit;
