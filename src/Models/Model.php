<?php
namespace App\Models;

use App\Core\Database;

abstract class Model
{
    abstract protected static function table(): string;
    abstract protected static function primaryKey(): string;

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM ' . static::table() . ' WHERE ' . static::primaryKey() . ' = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findAll(string $orderBy = ''): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT * FROM ' . static::table();
        if ($orderBy) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        return $pdo->query($sql)->fetchAll();
    }

    public static function findWhere(string $where, array $params = [], string $orderBy = ''): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT * FROM ' . static::table() . ' WHERE ' . $where;
        if ($orderBy) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findFirstWhere(string $where, array $params = []): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM ' . static::table() . ' WHERE ' . $where . ' LIMIT 1');
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public static function insert(array $data): int
    {
        $pdo = Database::connection();
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $pdo->prepare("INSERT INTO " . static::table() . " ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::connection();
        $sets = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $stmt = $pdo->prepare("UPDATE " . static::table() . " SET {$sets} WHERE " . static::primaryKey() . " = ?");
        $stmt->execute([...array_values($data), $id]);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("DELETE FROM " . static::table() . " WHERE " . static::primaryKey() . " = ?");
        $stmt->execute([$id]);
    }

    public static function count(string $where = '1 = 1', array $params = []): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM " . static::table() . " WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetch()['cnt'];
    }

    public static function raw(string $sql, array $params = []): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function rawOne(string $sql, array $params = []): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public static function execute(string $sql, array $params = []): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
}
