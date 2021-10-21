<?php
require(dirname(__FILE__) . '/../models/Todo.php');
require(dirname(__FILE__) . '/validations/TodoValidation.php');

session_start();
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

    public static function new()
    {
        $input['title'] = $_GET['title'];
        $input['detail'] = $_GET['detail'];
        $input['deadline_at'] = $_GET['deadline_at'];
        return $input;;
    }

    public static function store()
    {
        $title = $_POST['title'];
        $detail = $_POST['detail'];
        $deadline_at = $_POST['deadline_at'];

        $todoValidation = new TodoValidation;
        if (!$todoValidation->check($title, $detail, $deadline_at)) {
            $_SESSION['errors']  = $todoValidation->getErrorMessages();
            header("location: /../views/todo/new.php?title=" . $title . "&detail=" . $detail . "&deadline_at=" . $deadline_at);
            exit;
        }
        $data = $todoValidation->getData();
        $todo = new Todo();
        $todo->setTitle($data['title']);
        $todo->setDetail($data['detail']);
        $todo->setDeadline($data['deadline_at']);
        $todo->save();
        header("location: /../views/todo/index.php");
    }
}
