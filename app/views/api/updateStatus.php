<?php
require(dirname(__FILE__) . '/../../controllers/api/TodoController.php');
$result = TodoController::update_status();
header("Content-type: application/json; charset=UTF-8");
echo json_encode($result);
exit;
