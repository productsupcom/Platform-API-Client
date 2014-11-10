<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 10.11.14
 * Time: 15:37
 */

namespace Productsup\Utils;


class CommandLine {
    public static function infoText($message) {
        echo "\e[32m".$message."\e[0m".PHP_EOL;
    }
} 