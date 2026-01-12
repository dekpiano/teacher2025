<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class DbSchema extends Controller {
    public function index() {
        $db = \Config\Database::connect('personnel');
        try {
            $fields = $db->getFieldNames('tb_personnel_attendance');
            file_put_contents(WRITEPATH . 'db_schema.txt', implode("\n", $fields));
            echo "Success! Check writable/db_schema.txt";
        } catch (\Exception $e) {
            file_put_contents(WRITEPATH . 'db_error.txt', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }
}
