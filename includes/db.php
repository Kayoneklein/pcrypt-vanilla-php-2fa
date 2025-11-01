<?php

require_once dirname(__DIR__) . "/includes/db_tables.php";

$db_path = dirname(__DIR__) . '/database/pcrypt.db';

try {
    $pdo = new PDO("sqlite:$db_path");
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


    init_database_tables();

} catch (PDOException $e) {
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
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    
    );
");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS " . DBTables::$sessions . " (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER UNIQUE NOT NULL,
        token TEXT UNIQUE NOT NULL,
        expires_at DATETIME NOT NULL
    );
");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS " . DBTables::$two_fa . " (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER UNIQUE NOT NULL,
        public_key TEXT,
        server_url TEXT,
        device_id TEXT,
        server_id TEXT,
        device_token TEXT,
        is_mobile_device INTEGER,
        timestamp DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");
}