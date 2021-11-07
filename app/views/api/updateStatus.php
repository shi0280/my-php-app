<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
$todo_id = $_POST['todo_id'];
$status = $_POST['status'];
$result = TodoController::update_status($todo_id, $status);

header("Content-type: application/json; charset=UTF-8");
echo json_encode($result);
exit;
