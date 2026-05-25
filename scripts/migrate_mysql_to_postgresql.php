<?php
/**
 * Script de migración: MySQL → PostgreSQL
 *
 * Uso desde línea de comandos:
 *   php scripts/migrate_mysql_to_postgresql.php
 *
 * Variables de entorno opcionales:
 *   MYSQL_HOST, MYSQL_PORT, MYSQL_DB, MYSQL_USER, MYSQL_PASS
 *   PG_HOST, PG_PORT, PG_DB, PG_USER, PG_PASS
 */

declare(strict_types=1);

$mysqlConfig = [
    'host' => getenv('MYSQL_HOST') ?: '127.0.0.1',
    'port' => getenv('MYSQL_PORT') ?: '3306',
    'dbname' => getenv('MYSQL_DB') ?: 'sistema_pdvsa',
    'user' => getenv('MYSQL_USER') ?: 'root',
    'pass' => getenv('MYSQL_PASS') ?: '',
];

$pgConfig = [
    'host' => getenv('PG_HOST') ?: '127.0.0.1',
    'port' => getenv('PG_PORT') ?: '5432',
    'dbname' => getenv('PG_DB') ?: 'sistema_pdvsa',
    'user' => getenv('PG_USER') ?: 'postgres',
    'pass' => getenv('PG_PASS') ?: '',
];

function logMsg(string $msg): void
{
    echo '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
}

function connectMysql(array $cfg): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $cfg['host'],
        $cfg['port'],
        $cfg['dbname']
    );
    return new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function connectPostgres(array $cfg): PDO
{
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $cfg['host'],
        $cfg['port'],
        $cfg['dbname']
    );
    return new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function ensurePostgresSchema(PDO $pg): void
{
    $schemaFile = dirname(__DIR__) . '/sql/schema_postgresql.sql';
    if (!is_file($schemaFile)) {
        throw new RuntimeException('No se encontró schema_postgresql.sql');
    }

    $sql = file_get_contents($schemaFile);
    $sql = preg_replace('/^CREATE DATABASE.*$/m', '', $sql);
    $sql = preg_replace('/^\\\\c\s+\w+.*$/m', '', $sql);

    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => $s !== '' && !str_starts_with($s, '--')
    );

    foreach ($statements as $statement) {
        if ($statement !== '') {
            $pg->exec($statement);
        }
    }
}

function migrateTable(PDO $mysql, PDO $pg, string $table, array $columns): int
{
    $rows = $mysql->query("SELECT * FROM {$table}")->fetchAll();
    if (count($rows) === 0) {
        logMsg("Tabla {$table}: sin registros.");
        return 0;
    }

    $pg->exec("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE");

    $colList = implode(', ', $columns);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $stmt = $pg->prepare("INSERT INTO {$table} ({$colList}) VALUES ({$placeholders})");

    $count = 0;
    foreach ($rows as $row) {
        $values = [];
        foreach ($columns as $col) {
            $val = $row[$col] ?? null;
            if ($col === 'permisos_json' && is_string($val)) {
                $val = $val ?: '{}';
            }
            $values[] = $val;
        }
        $stmt->execute($values);
        $count++;
    }

    $maxId = (int) $mysql->query("SELECT COALESCE(MAX(id), 0) AS m FROM {$table}")->fetch()['m'];
    if ($maxId > 0) {
        $pg->exec("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$maxId})");
    }

    logMsg("Tabla {$table}: {$count} registros migrados.");
    return $count;
}

try {
    logMsg('Iniciando migración MySQL → PostgreSQL...');

    $mysql = connectMysql($mysqlConfig);
    $pg = connectPostgres($pgConfig);

    logMsg('Verificando esquema en PostgreSQL...');
    ensurePostgresSchema($pg);

    $pg->beginTransaction();

    migrateTable($mysql, $pg, 'roles', [
        'id', 'nombre', 'permisos_json', 'descripcion', 'estado', 'creado_en', 'actualizado_en',
    ]);

    migrateTable($mysql, $pg, 'usuarios', [
        'id', 'rol_id', 'nombre_completo', 'cargo', 'email', 'telefono_extension',
        'nombre_usuario', 'contrasena_hash', 'foto_perfil', 'estado', 'ultimo_acceso',
        'creado_por', 'fecha_creacion', 'sesion_activa_token', 'sesion_expira_en', 'actualizado_en',
    ]);

    $pg->commit();
    logMsg('Migración completada correctamente.');
} catch (Throwable $e) {
    if (isset($pg) && $pg->inTransaction()) {
        $pg->rollBack();
    }
    logMsg('ERROR: ' . $e->getMessage());
    exit(1);
}
