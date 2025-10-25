<?php


defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', dirname(__DIR__));
defined('CORE_PATH') ? null : define('CORE_PATH', SITE_ROOT . DS . 'core');
defined('INC_PATH') ? null : define('INC_PATH', SITE_ROOT . DS . 'includes');
defined('MODEL_PATH') ? null : define('MODEL_PATH', SITE_ROOT . DS . 'models');
defined('API_PATH') ? null : define('API_PATH', SITE_ROOT . DS . 'api');
defined('SERVICE_PATH') ? null : define('SERVICE_PATH', SITE_ROOT . DS . 'services');
defined('V1') ? null : define('V1', API_PATH . DS . 'v1');


// header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// INCLUDE PATH START
require_once INC_PATH . DS . 'db.php';
require_once INC_PATH . DS . 'db_tables.php';
// INCLUDE PATH END

// APP CORE PATH START
require_once CORE_PATH . DS . 'db_conn.php';
require_once CORE_PATH . DS . 'http_request.php';
require_once CORE_PATH . DS . 'http_response.php';
require_once CORE_PATH . DS . 'utils.php';
// APP CORE PATH END

// APP MODELS PATH START
require_once MODEL_PATH . DS . 'user_model.php';
require_once MODEL_PATH . DS . 'encrypt_data.php';
// APP MODELS PATH END

// V1 PATH START
require_once V1 . DS . 'user.php';
require_once V1 . DS . '2fa.php';
// V1 PATH END

// APP SERVICE PATH START
require_once SERVICE_PATH . DS . 'security_service.php';
// APP SERVICE PATH END
