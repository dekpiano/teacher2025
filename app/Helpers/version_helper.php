<?php

if (!function_exists('get_git_version')) {
    /**
     * Retrieve Git version details automatically.
     * Works with Git CLI, direct .git filesystem parsing (Docker friendly), and file fallback.
     *
     * @return array
     */
    function get_git_version(): array
    {
        static $cachedVersion = null;
        if ($cachedVersion !== null) {
            return $cachedVersion;
        }

        $rootDir = defined('ROOTPATH') ? realpath(ROOTPATH) : realpath(__DIR__ . '/../../');
        $gitDir = $rootDir ? ($rootDir . DIRECTORY_SEPARATOR . '.git') : '';

        $branch = 'main';
        $shortHash = 'unknown';
        $fullHash = '';
        $commitTime = time();
        $commitMessage = 'Initial release';
        $commitCount = 1;

        // ── Strategy 1: Direct .git filesystem parser (Docker & Fast, no exec needed) ──
        if (is_dir($gitDir)) {
            // 1. Get HEAD & Branch
            $headFile = $gitDir . DIRECTORY_SEPARATOR . 'HEAD';
            if (is_file($headFile)) {
                $headContent = trim((string)@file_get_contents($headFile));
                if (str_starts_with($headContent, 'ref: ')) {
                    $refPath = substr($headContent, 5);
                    $branch = basename($refPath);

                    // Read ref file to get commit hash
                    $refFile = $gitDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $refPath);
                    if (is_file($refFile)) {
                        $fullHash = trim((string)@file_get_contents($refFile));
                    } elseif (is_file($gitDir . DIRECTORY_SEPARATOR . 'packed-refs')) {
                        // Check packed-refs if ref file doesn't exist directly
                        $packed = @file_get_contents($gitDir . DIRECTORY_SEPARATOR . 'packed-refs');
                        if ($packed && preg_match('/^([0-9a-f]{40})\s+' . preg_quote($refPath, '/') . '$/m', $packed, $m)) {
                            $fullHash = $m[1];
                        }
                    }
                } else {
                    $fullHash = $headContent;
                    $branch = 'detached';
                }
            }

            // 2. Parse .git/logs/HEAD for latest commit message, timestamp, and commit count
            $logFile = $gitDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'HEAD';
            if (is_file($logFile)) {
                $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if (!empty($lines)) {
                    $commitCount = count($lines);
                    $lastLine = end($lines);

                    // Format: <hash1> <hash2> <name> <email> <timestamp> <tz>\t<type>: <message>
                    if (preg_match('/^([0-9a-f]{40})\s+([0-9a-f]{40})\s+.*?\s+(\d+)\s+([+-]\d{4})\t(?:commit(?:\s*\(.*?\))?:\s*)?(.*)$/i', $lastLine, $matches)) {
                        if (empty($fullHash)) {
                            $fullHash = $matches[2];
                        }
                        $commitTime = (int)$matches[3];
                        $commitMessage = trim($matches[5]);
                    }
                }
            }

            if (!empty($fullHash)) {
                $shortHash = substr($fullHash, 0, 7);
            }
        }

        // ── Strategy 2: CLI Git fallback if available ──
        if ($shortHash === 'unknown' && function_exists('shell_exec')) {
            try {
                $cliHash = @shell_exec('git rev-parse --short HEAD 2>&1');
                if ($cliHash && !str_contains($cliHash, 'fatal:') && !str_contains($cliHash, 'not found') && !str_contains($cliHash, 'not recognized')) {
                    $shortHash = trim($cliHash);
                    $fullHash = trim((string)@shell_exec('git rev-parse HEAD 2>&1'));
                    $branch = trim((string)@shell_exec('git rev-parse --abbrev-ref HEAD 2>&1')) ?: $branch;
                    $cliTime = trim((string)@shell_exec('git log -1 --format="%ct" 2>&1'));
                    if (is_numeric($cliTime)) {
                        $commitTime = (int)$cliTime;
                    }
                    $commitMessage = trim((string)@shell_exec('git log -1 --format="%s" 2>&1')) ?: $commitMessage;
                    $cliCount = trim((string)@shell_exec('git rev-list --count HEAD 2>&1'));
                    if (is_numeric($cliCount)) {
                        $commitCount = (int)$cliCount;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore CLI failures
            }
        }

        // ── Strategy 3: File timestamp fallback ──
        if ($commitTime <= 0) {
            $commitTime = @filemtime(__FILE__) ?: time();
        }

        // Thai Buddhist Era Date formatting
        $thaiDate = function_exists('thai_date') 
            ? thai_date($commitTime, 'short') 
            : date('d/m/', $commitTime) . (date('Y', $commitTime) + 543);

        $thaiTime = date('H:i', $commitTime) . ' น.';
        $thaiDateTime = $thaiDate . ' ' . $thaiTime;

        // Version string (e.g. v1.0.66 or v2026.09.12)
        $semVer = 'v1.0.' . $commitCount;

        $cachedVersion = [
            'version'       => $semVer,
            'hash'          => $shortHash,
            'full_hash'     => $fullHash,
            'branch'        => $branch,
            'message'       => $commitMessage,
            'timestamp'     => $commitTime,
            'date'          => $thaiDate,
            'time'          => $thaiTime,
            'datetime'      => $thaiDateTime,
            'commit_count'  => $commitCount,
        ];

        return $cachedVersion;
    }
}
