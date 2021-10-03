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
        $todo = Todo::findById();
        return $todo;
    }
}
