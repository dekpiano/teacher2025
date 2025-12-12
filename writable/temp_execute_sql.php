<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'skjacth_academic';

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents('create_new_objective_tables.sql');
    $pdo->exec($sql);

    echo "Tables 'tb_club_objectives', 'tb_club_student_progress', and 'tb_club_student_summary' created successfully.";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>