<?php
require(dirname(__FILE__) . '/../models/Todo.php');

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
        $todo = Todo::findById($id);
        if (!$todo) {
            header("location: /../views/error/404.php");
            return;
        }
        return $todo;
    }
}
