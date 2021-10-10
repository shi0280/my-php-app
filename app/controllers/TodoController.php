<?php
require(dirname(__FILE__) . '/../models/Todo.php');
require(dirname(__FILE__) . '/validations/TodoValidation.php');

if (isset($_POST['store'])) {
    TodoController::store();
}
class TodoController
{
    public static function index()
    {
        $todos = Todo::findAll();
        return $todos;
    }

    public static function detail()
    {
        $id = $_GET['todo_id'];
        if (empty($id)) {
            header("location: /../views/error/404.php");
            return;
        }
        $todo = Todo::findById($id);
        if (!$todo) {
            header("location: /../views/error/404.php");
            return;
        }
        return $todo;
    }

    public static function store()
    {
        $title = $_POST['title'];
        $detail = $_POST['detail'];
        $deadline_at = $_POST['deadline_at'];

        $todoValidation = new TodoValidation;
        if (!$todoValidation->check($title)) {
            $errors = $todoValidation->getErrorMessages();
            header("location: /../views/todo/new.php");
        } else {
            $posts['title'] = $title;
            $posts['detail'] = $detail;
            $posts['deadline_at'] = $deadline_at;
            Todo::store($title, $detail, $deadline_at);
            header("location: /../views/todo/index.php");
        }
    }
}
