<?php
class DBexception extends Error{
    public function __construct(string $mensaje){
        parent::__construct($mensaje);
    }
}