<?php
require_once 'core/app.php';

$STATIC_V1_PATH = '/api/v1';
// $STATIC_V1_PATH = '/php-projects/pcrypt-php/api/v1';


// Get the requested path
// /php-projects/pcrypt-php/api/v1/users.php
$req = new HttpRequest();
$URI = $req->uri;
$host = $req->host;
$METHOD = $req->method;
$BODY = $req->body;

// echo $URI . " " . $host;


$URI = str_replace('.php', '', $URI);

// Remove leading /php-projects/pcrypt-php/api/v1
$URI = str_replace($STATIC_V1_PATH, '', $URI);

///Filter out the query parameters from the request uri
$params = explode('?', trim($URI, '?'), );
if (count($params) > 1) {
    /// Set the first part if the query parameter as the main resource path
    $URI = $params[0];
    ///Remove the fist part of the uri before the query parameters
    unset($params[0]);

    ///split the parameters further with each given query parameter
    $params = explode('&', trim($URI, '&'), );
}

// var_dump($params);
// Split the path
// eg [users, userId, params]
$segments = explode('/', trim($URI, '/'));



$RESOURCE_PATH = $segments[0] ?? '';

$AUTH_USER;

///ROUTE PROTECTION
$RESOURCE_ROUTE = $segments[1] ?? '';

if (
    $RESOURCE_PATH === 'healthcheck' ||
    ($RESOURCE_PATH === 'users' && ($RESOURCE_ROUTE === 'login' ||
        $RESOURCE_ROUTE === 'register') && $METHOD === 'POST')
) {
    /// THE LOGIN AND REGISTER ROUTE WOULD NOT NEED ANY AUTHENTICATION OR PROTECTION
} else {
    /// CHECK ACCESS TOKEN CREDENTIALS
    $AUTH_USER = protectRoute();
}

// Basic router
switch ($RESOURCE_PATH) {
    case 'healthcheck':
        http_response(200, 'The application endpoint is working well');
        break;
    case 'users':
        $is_valid_url = false;

        if ($METHOD === 'GET') {
        }

        if ($METHOD === 'POST') {
            if (!isset($BODY)) {
                http_response(400, 'Invalid user payload');
            }

            if ($RESOURCE_ROUTE === 'login') {
                $is_valid_url = true;
                UserController::login($BODY);
                break;
            }

            if ($RESOURCE_ROUTE === 'register') {
                $is_valid_url = true;
                UserController::register($BODY);
                break;
            }
        }

        if (!$is_valid_url) {
            http_response(404, 'Endpoint not found');
        }
        break;

    case '2fa':
        $is_valid_url = false;
        if ($METHOD === 'POST') {
            if (!isset($BODY)) {
                http_response(400, 'Invalid user payload');
            }
            $twoFaController = new TwoFaController();

            if ($RESOURCE_ROUTE === 'activate') {
                $is_valid_url = true;
                $user = User::fromAssoc($AUTH_USER);

                $twoFaController->activate($user, $BODY);
                break;
            }

            if ($RESOURCE_ROUTE === 'change-device') {
            }
        }

        if (!$is_valid_url) {
            http_response(404, 'Endpoint not found');
        }
        break;

    default:
        http_response(404, 'Endpoint not found');
        break;
}


