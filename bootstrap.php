<?php
require_once __DIR__ . '/classes/CapacitorType.php';
require_once __DIR__ . '/classes/Parameter.php';
require_once __DIR__ . '/classes/Capacitor.php';
require_once __DIR__ . '/classes/CapacitorRepository.php';

$pdo = new PDO(
    'mysql:host=localhost;dbname=capacitors_db;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$repository = new CapacitorRepository($pdo);
