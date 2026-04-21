<?php

namespace src\backend\libs;

class PDO
{
    /* PDO parameter constants */
    public const PARAM_NULL = 0;
    public const PARAM_INT = 1;
    public const PARAM_STR = 2;
    public const PARAM_LOB = 3;
    public const PARAM_STMT = 4;
    public const PARAM_BOOL = 5;

    /* PDO fetch mode constants */
    public const FETCH_LAZY = 1;
    public const FETCH_ASSOC = 2;
    public const FETCH_NUM = 3;
    public const FETCH_BOTH = 4;
    public const FETCH_OBJ = 5;
    public const FETCH_BOUND = 6;
    public const FETCH_COLUMN = 7;
    public const FETCH_CLASS = 8;
    public const FETCH_INTO = 9;

    /* PDO error mode constants */
    public const ERRMODE_SILENT = 0;
    public const ERRMODE_WARNING = 1;
    public const ERRMODE_EXCEPTION = 2;

    /* PDO attribute constants */
    public const ATTR_ERRMODE = 3;
    public const ATTR_DEFAULT_FETCH_MODE = 19;

    /* PDO cursor constants */
    public const CURSOR_FWDONLY = 0;
    public const CURSOR_SCROLL = 1;

    /* PDO other constants */
    public const ERR_NONE = '00000';

    private PDO $pdo;

    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        ?array $options = null
    ) {
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options ?? []);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function prepare(string $query, array $options = []): PDOStatement{
    $stmt = $this->pdo->prepare($query, $options);

    if ($stmt === false) {
        throw new PDOException("Failed to prepare statement");
    }

    return new PDOStatement($stmt);
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    public function exec(string $statement): int|false
    {
        return $this->pdo->exec($statement);
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$args): PDOStatement|false
    {
        $stmt = $fetchMode === null
            ? $this->pdo->query($query)
            : $this->pdo->query($query, $fetchMode, ...$args);

        return $stmt === false ? false : new PDOStatement($stmt);
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return $this->pdo->lastInsertId($name);
    }

    public function errorCode(): ?string
    {
        return $this->pdo->errorCode();
    }

    public function errorInfo(): array
    {
        return $this->pdo->errorInfo();
    }

    public function getAttribute(int $attribute): mixed
    {
        return $this->pdo->getAttribute($attribute);
    }

    public function setAttribute(int $attribute, mixed $value): bool
    {
        return $this->pdo->setAttribute($attribute, $value);
    }

    public static function getAvailableDrivers(): array
    {
        return PDO::getAvailableDrivers();
    }
}
