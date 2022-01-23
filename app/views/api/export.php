<?php
require(dirname(__FILE__) . '/../../controllers/api/TodoController.php');
if (isset($_POST['export'])) {
    TodoController::export();
}
