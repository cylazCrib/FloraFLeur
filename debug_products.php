<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=flora_fleur', 'root', '');
$stmt = $pdo->query("SELECT id,name,image FROM products LIMIT 20");
foreach ($stmt as $row) {
    echo $row['id'] . ' | ' . $row['name'] . ' | ' . $row['image'] . "\n";
}
