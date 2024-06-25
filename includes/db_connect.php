<?php

declare(strict_types=1);

// $root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

// define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
// define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR);

try {
    $db = new PDO('mysql:host=localhost;dbname=school', 'root', '',[
        // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    // $query = 'SELECT * FROM classes';
    // foreach ($db->query($query) as $user) {
    //     echo '<pre>';
    //     var_dump($user);
    //     echo '<pre>';
    // }
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
