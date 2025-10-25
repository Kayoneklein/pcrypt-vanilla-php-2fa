<?php

class HttpRequest
{
    public $body;
    public $host;
    public $uri;
    public $method;

    public function __construct()
    {
        $this->body = $this->get_body();
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->host = $_SERVER['HTTP_HOST'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function get_body()
    {
        $req_body = file_get_contents('php://input');
        if ($req_body) {
            return json_decode($req_body, true);
        }
        return null;
    }
}