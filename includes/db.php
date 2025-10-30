<?php

// $host = getenv('DB_HOST');
// $user = getenv('DB_USER');
// $password = getenv('DB_PASSWORD');
// $dbname = getenv('DB_NAME');

require_once dirname(__DIR__) . "/includes/db_tables.php";

$dbPath = dirname(__DIR__) . '/database/pcrypt.db';

try {
    $pdo = new PDO("sqlite:$dbPath");
    // $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


    init_database_tables();

} catch (PDOException $e) {
    // print_r("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());

}

function init_database_tables()
{
    global $pdo;
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS " . DBTables::$users . " (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        public_key TEXT,
        server_url TEXT,
        device_id TEXT,
        server_id TEXT,
        device_token TEXT,
        is_mobile_device INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        timestamp DATETIME
    );
");
}