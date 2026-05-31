<?php
/**
 * Conexión a la base de datos — delega en App\Core\Database.
 * Compatibilidad legacy: mantiene getDbConnection() y constantes DB_*.
 */

require_once __DIR__ . '/autoload.php';

use App\Core\App;
use App\Core\Database;

App::getInstance();

define('DB_DRIVER', getenv('DB_DRIVER') ?: 'mysql');
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: (DB_DRIVER === 'pgsql' ? '5432' : '3306'));
define('DB_NAME', getenv('DB_NAME') ?: 'sistema_pdvsa');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

function getDbConnection(): PDO
{
    return Database::connection();
}
