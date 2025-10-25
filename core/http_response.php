<?php

function http_response(int $status, $data)
{
    http_response_code($status);
    echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}