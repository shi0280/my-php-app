<?php
require( dirname(__FILE__) . '/../models/Todo.php' );

class TodoController{

    public function index() {
        $allTodos = Todo::findAll();
        return $allTodos;
    }
}
?>