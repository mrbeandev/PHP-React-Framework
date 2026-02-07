<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Core\Support\Config;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

Config::setBasePath(__DIR__);

// Error Logging Configuration
$logFile = __DIR__ . '/php_errors.log';
ini_set('log_errors', '1');
ini_set('error_log', $logFile);
$displayErrors = Config::get('app.debug', false) ? '1' : '0';
ini_set('display_errors', $displayErrors);
error_reporting(E_ALL);

// Ensure log file exists and is writable
if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0666);
}

$capsule = new Capsule;

try {
    $connection = (string) Config::get('database.connection', 'sqlite');
    $prefix = (string) Config::get('database.prefix', '');

    if ($connection === 'sqlite') {
        $dbPath = (string) Config::get('database.sqlite.database', 'database/database.sqlite');

        if (!str_starts_with($dbPath, '/')) {
            $dbPath = __DIR__ . '/' . ltrim($dbPath, '/');
        }

        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0755, true);
        }

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => $prefix,
        ]);
    } else {
        $capsule->addConnection([
            'driver' => $connection,
            'host' => (string) Config::get('database.mysql.host', '127.0.0.1'),
            'port' => (string) Config::get('database.mysql.port', '3306'),
            'database' => (string) Config::get('database.mysql.database', ''),
            'username' => (string) Config::get('database.mysql.username', ''),
            'password' => (string) Config::get('database.mysql.password', ''),
            'charset' => (string) Config::get('database.mysql.charset', 'utf8mb4'),
            'collation' => (string) Config::get('database.mysql.collation', 'utf8mb4_unicode_ci'),
            'prefix' => $prefix,
        ]);
    }

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
} catch (\Exception $e) {
    error_log("[TaskFlow Bootstrap Error] " . $e->getMessage());
    if (php_sapi_name() === 'cli') {
        echo "❌ Database Error: " . $e->getMessage() . "\n";
    } else {
        http_response_code(500);
        die("Backend configuration error. Please check server logs.");
    }
}
