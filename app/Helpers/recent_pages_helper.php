<?php

/**
 * Helper for tracking recently accessed pages
 * Uses UPSERT pattern - updates timestamp if exists, inserts if new
 */

if (!function_exists('track_recent_page')) {
    /**
     * Track a page visit for the current user
     * 
     * @param string $pageUrl   The URL/route of the page
     * @param string $pageName  Display name for the page
     * @param string $pageIcon  Bootstrap icon class (e.g., 'bi-calendar-check')
     */
    function track_recent_page(string $pageUrl, string $pageName, string $pageIcon = 'bi-link-45deg')
    {
        try {
            $session = session();
            if (!$session->get('isLoggedIn')) {
                return;
            }

            $pers_id = $session->get('person_id');
            if (!$pers_id) {
                return;
            }

            $db = \Config\Database::connect('personnel');
            
            // Check if record exists
            $existing = $db->table('tb_recent_pages')
                ->where('pers_id', $pers_id)
                ->where('page_url', $pageUrl)
                ->get()
                ->getRow();

            if ($existing) {
                // Update timestamp only
                $db->table('tb_recent_pages')
                    ->where('id', $existing->id)
                    ->update([
                        'last_accessed' => date('Y-m-d H:i:s'),
                        'page_name' => $pageName,
                        'page_icon' => $pageIcon,
                    ]);
            } else {
                // Insert new record
                $db->table('tb_recent_pages')->insert([
                    'pers_id' => $pers_id,
                    'page_url' => $pageUrl,
                    'page_name' => $pageName,
                    'page_icon' => $pageIcon,
                    'last_accessed' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'track_recent_page error: ' . $e->getMessage());
        }
    }
}

if (!function_exists('get_recent_pages')) {
    /**
     * Get recent pages for the current user
     * 
     * @param int $limit  Number of recent pages to retrieve
     * @return array
     */
    function get_recent_pages(int $limit = 2): array
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return [];
        }

        $pers_id = $session->get('person_id');
        if (!$pers_id) {
            return [];
        }

        $db = \Config\Database::connect('personnel');
        
        return $db->table('tb_recent_pages')
            ->where('pers_id', $pers_id)
            ->orderBy('last_accessed', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
