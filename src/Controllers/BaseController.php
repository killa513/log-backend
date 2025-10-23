<?php
namespace App\Controllers;

use PDO;

abstract class BaseController {
    protected static PDO $pdo;

    public static function setPDO(PDO $pdo): void {
        self::$pdo = $pdo;
    }
}
