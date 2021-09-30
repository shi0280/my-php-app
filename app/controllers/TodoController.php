<?php
require( dirname(__FILE__) . '/../models/Todo.php' );

class TodoController{

    public function index() {
        $todo = new Todo();
        $allTodos = $todo->findAll();
        return $allTodos;
    }
}
?>