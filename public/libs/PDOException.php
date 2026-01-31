<?php

namespace public\libs;

use Exception;

class PDOException extends Exception
{
    public ?array $errorInfo = null;
}
