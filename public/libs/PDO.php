<?php

class PDO
{
    public const PARAM_NULL = 0;
    public const PARAM_INT = 1;
    public const PARAM_STR = 2;
    public const PARAM_LOB = 3;
    public const PARAM_STMT = 4;
    public const PARAM_BOOL = 5;

    public const FETCH_LAZY = 1;
    public const FETCH_ASSOC = 2;
    public const FETCH_NUM = 3;
    public const FETCH_BOTH = 4;
    public const FETCH_OBJ = 5;
    public const FETCH_BOUND = 6;
    public const FETCH_COLUMN = 7;
    public const FETCH_CLASS = 8;
    public const FETCH_INTO = 9;

    public const ERRMODE_SILENT = 0;
    public const ERRMODE_WARNING = 1;
    public const ERRMODE_EXCEPTION = 2;

    public const CASE_NATURAL = 0;
    public const CASE_LOWER = 2;
    public const CASE_UPPER = 1;

    public const NULL_NATURAL = 0;
    public const NULL_EMPTY_STRING = 1;
    public const NULL_TO_STRING = 2;

    public const ATTR_AUTOCOMMIT = 0;
    public const ATTR_PREFETCH = 1;
    public const ATTR_TIMEOUT = 2;
    public const ATTR_ERRMODE = 3;
    public const ATTR_SERVER_VERSION = 4;
    public const ATTR_CLIENT_VERSION = 5;
    public const ATTR_SERVER_INFO = 6;
    public const ATTR_CONNECTION_STATUS = 7;
    public const ATTR_CASE = 8;
    public const ATTR_CURSOR_NAME = 9;
    public const ATTR_CURSOR = 10;
    public const ATTR_ORACLE_NULLS = 11;
    public const ATTR_PERSISTENT = 12;
    public const ATTR_STATEMENT_CLASS = 13;
    public const ATTR_FETCH_TABLE_NAMES = 14;
    public const ATTR_FETCH_CATALOG_NAMES = 15;
    public const ATTR_DRIVER_NAME = 16;
    public const ATTR_STRINGIFY_FETCHES = 17;
    public const ATTR_MAX_COLUMN_LEN = 18;
    public const ATTR_DEFAULT_FETCH_MODE = 19;
    public const ATTR_EMULATE_PREPARES = 20;

    public const CURSOR_FWDONLY = 0;
    public const CURSOR_SCROLL = 1;

    public const ERR_NONE = '00000';

    public function __construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)
    {
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        return false;
    }

    public function beginTransaction(): bool
    {
        return false;
    }

    public function commit(): bool
    {
        return false;
    }

    public function rollBack(): bool
    {
        return false;
    }

    public function inTransaction(): bool
    {
        return false;
    }

    public function exec(string $statement): int|false
    {
        return false;
    }

    public function query(string $query, ?int $fetchMode = null, ...$args): PDOStatement|false
    {
        return false;
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return false;
    }

    public function errorCode(): ?string
    {
        return null;
    }

    public function errorInfo(): array
    {
        return [];
    }

    public function getAttribute(int $attribute): mixed
    {
        return null;
    }

    public function setAttribute(int $attribute, mixed $value): bool
    {
        return false;
    }

    public static function getAvailableDrivers(): array
    {
        return [];
    }
}

class PDOStatement
{
    public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool
    {
        return false;
    }

    public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        return false;
    }

    public function execute(?array $params = null): bool
    {
        return false;
    }

    public function fetch(int $mode = PDO::FETCH_BOTH, int $cursorOrientation = 0, int $cursorOffset = 0): mixed
    {
        return null;
    }

    public function fetchAll(int $mode = PDO::FETCH_BOTH, ...$args): array
    {
        return [];
    }

    public function fetchColumn(int $column = 0): mixed
    {
        return null;
    }

    public function rowCount(): int
    {
        return 0;
    }

    public function errorCode(): ?string
    {
        return null;
    }

    public function errorInfo(): array
    {
        return [];
    }

    public function closeCursor(): bool
    {
        return false;
    }

    public function setFetchMode(int $mode, ...$args): bool
    {
        return false;
    }
}

class PDOException extends Exception
{
    public ?array $errorInfo = null;
}
