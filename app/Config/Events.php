<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        // Disable ONLY_FULL_GROUP_BY mode for all database connections
        $db = \Config\Database::connect();
        $db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        
        // Also disable for other database groups
        try {
            $dbPersonnel = \Config\Database::connect('personnel');
            $dbPersonnel->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        } catch (\Exception $e) {
            // Ignore if connection fails
        }
        
        try {
            $dbSkj = \Config\Database::connect('skj');
            $dbSkj->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        } catch (\Exception $e) {
            // Ignore if connection fails
        }
        
        try {
            $dbAffairs = \Config\Database::connect('affairs');
            $dbAffairs->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        } catch (\Exception $e) {
            // Ignore if connection fails
        }
    }
});

/*
 * --------------------------------------------------------------------
 * Debug Toolbar Listeners.
 * --------------------------------------------------------------------
 * If you delete, they will no longer be collected.
 */
if (CI_DEBUG && !is_cli()) {
    Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
    Services::toolbar()->respond();
}
