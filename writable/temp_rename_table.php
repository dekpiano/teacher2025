<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'skjacth_academic';

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("RENAME TABLE tb_club_recoed_activity TO tb_club_record_activity;");
    echo "Table tb_club_recoed_activity renamed to tb_club_record_activity successfully.\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
