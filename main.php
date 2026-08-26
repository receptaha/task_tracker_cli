<?php

require_once "functions.php";

try {
    handle_query($argc, $argv);
}catch(\Exception $e) {
    echo "\n{$e->getMessage()}";
}