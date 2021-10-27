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
        // GETパラメータがあったら取得する
        $input['title'] = $_GET['title'];
        $input['detail'] = $_GET['detail'];
        $input['deadline_at'] = $_GET['deadline_at'];
        // エラーがあったら取得する
        $errors = array();
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];
        return [$input, $errors];
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
        $result = $todo->save();
        if ($result === true) {
            header("location: /../views/todo/index.php");
            return $result;
        } else {
            return $result;
        }
    }

    public static function edit()
    {
        // DBからtodoを取得する
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

        // GETパラメータがあったら取得する
        $input['title'] = $_GET['title'];
        $input['detail'] = $_GET['detail'];
        $input['deadline_at'] = $_GET['deadline_at'];
        $input['status'] = $_GET['status'];

        // エラーがあったら取得する
        $errors = array();
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];

        return [$todo, $input, $errors];
    }


    public static function update($todo_id)
    {
        $title = $_POST['title'];
        $detail = $_POST['detail'];
        $deadline_at = $_POST['deadline_at'];
        $status = $_POST['status'];

        $todoValidation = new TodoValidation;
        if (!$todoValidation->check($title, $detail, $deadline_at)) {
            $_SESSION['errors']  = $todoValidation->getErrorMessages();
            header("location: /../views/todo/edit.php?todo_id=" . $todo_id . "title=" . $title . "&detail=" . $detail . "&deadline_at=" . $deadline_at . "&status=" . $status);
            exit;
        }
        $data = $todoValidation->getData();
        $todo = new Todo();
        $todo->setTitle($data['title']);
        $todo->setDetail($data['detail']);
        $todo->setDeadline($data['deadline_at']);
        $result = $todo->save($todo_id);
        if ($result === true) {
            header("location: /../views/todo/index.php");
            return $result;
        } else {
            return $result;
        }
    }
}
