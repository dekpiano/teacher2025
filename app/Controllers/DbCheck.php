<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class DbCheck extends Controller {
    public function index() {
        $db = \Config\Database::connect();
        
        $tables = ['tb_register', 'tb_students', 'tb_subjects'];
        foreach ($tables as $table) {
            echo "Table: $table\n";
            $query = $db->query("SHOW INDEX FROM $table");
            print_r($query->getResultArray());
            echo "\n";
            
            $count = $db->table($table)->countAllResults();
            echo "Count: $count\n\n";
        }
    }
}
