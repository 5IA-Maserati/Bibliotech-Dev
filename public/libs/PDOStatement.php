<?php

namespace App\Database;

use PDO;

class PDOStatement
{
    public function bindValue(
        string|int $param,
        mixed $value,
        int $type = PDO::PARAM_STR
    ): bool {
        return false;
    }

    public function bindParam(
        string|int $param,
        mixed &$var,
        int $type = PDO::PARAM_STR,
        int $maxLength = 0,
        mixed $driverOptions = null
    ): bool {
        return false;
    }
}
