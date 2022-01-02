<?php
require(dirname(__FILE__) . '/../../controllers/api/TodoController.php');
$result = TodoController::create_csv();
header("Content-type: application/json; charset=UTF-8");
echo json_encode($result);
exit;
