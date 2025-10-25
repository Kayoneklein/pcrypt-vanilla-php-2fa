<?php

class DbConnection
{
    protected $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }
}