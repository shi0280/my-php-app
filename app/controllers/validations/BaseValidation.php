<?php
class BaseValidation {

    protected  $errors = [];
    public function getErrorMessages(){
        return $this->errors;
    } 
}
