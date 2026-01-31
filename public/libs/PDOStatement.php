<?php

namespace public\libs;

use PDOStatement as NativePDOStatement;

class PDOStatement
{
    private NativePDOStatement $stmt;

    public function __construct(NativePDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function bindValue(
        string|int $param,
        mixed $value,
        int $type = \PDO::PARAM_STR
    ): bool {
        return $this->stmt->bindValue($param, $value, $type);
    }

    public function bindParam(
        string|int $param,
        mixed &$var,
        int $type = \PDO::PARAM_STR,
        int $maxLength = 0,
        mixed $driverOptions = null
    ): bool {
        return $this->stmt->bindParam(
            $param,
            $var,
            $type,
            $maxLength,
            $driverOptions
        );
    }

    public function execute(?array $params = null): bool
    {
        return $this->stmt->execute($params);
    }

    public function fetch(
        int $mode = \PDO::FETCH_BOTH,
        int $cursorOrientation = \PDO::FETCH_ORI_NEXT,
        int $cursorOffset = 0
    ): mixed {
        return $this->stmt->fetch(
            $mode,
            $cursorOrientation,
            $cursorOffset
        );
    }

    public function fetchAll(int $mode = \PDO::FETCH_BOTH, mixed ...$args): array
    {
        return $this->stmt->fetchAll($mode, ...$args);
    }

    public function fetchColumn(int $column = 0): mixed
    {
        return $this->stmt->fetchColumn($column);
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function errorCode(): ?string
    {
        return $this->stmt->errorCode();
    }

    public function errorInfo(): array
    {
        return $this->stmt->errorInfo();
    }

    public function closeCursor(): bool
    {
        return $this->stmt->closeCursor();
    }

    public function setFetchMode(int $mode, mixed ...$args): bool
    {
        return $this->stmt->setFetchMode($mode, ...$args);
    }
}
