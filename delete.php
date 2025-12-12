<?php
 header("Access-Control-Allow-Origin: *"); // หรือระบุโดเมนของคุณเพื่อความปลอดภัย
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // 2. จัดการกับ Preflight Request (OPTIONS)
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      http_response_code(200);
      exit();
  }

  header('Content-Type: application/json');

  $response = [];

  try {
      $json_data = file_get_contents('php://input');
      $data = json_decode($json_data, true);

      if (isset($data['files']) && is_array($data['files']) && isset($data['path'])) {
          // *IMPORTANT*: Set your base upload directory (must match upload.php)
          $baseDir = '/var/www/html/uploads/'; // ตรวจสอบให้แน่ใจว่า Path นี้ถูกต้องบน Server ของคุณ
          $subDir = trim($data['path'], '/');
          $targetDir = $baseDir . $subDir;

          $deletedFiles = [];
          $failedFiles = [];

          foreach ($data['files'] as $filename) {
              // Security check to prevent directory traversal
              if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
                  $failedFiles[] = [
                      'filename' => $filename,
                      'error' => 'Security check failed: Invalid filename or path traversal attempt.'
                  ];
                  continue;
              }

              $filePath = $targetDir . '/' . $filename;

              if (file_exists($filePath)) {
                  if (unlink($filePath)) {
                      $deletedFiles[] = $filename;
                  } else {
                      // Capture PHP's last error message for more details
                      $lastError = error_get_last();
                      $failedFiles[] = [
                          'filename' => $filename,
                          'error' => 'Failed to unlink file (permissions issue?). PHP Error: ' . ($lastError['message'] ?? 'Unknown error.')
                      ];
                  }
              } else {
                  $failedFiles[] = [
                      'filename' => $filename,
                      'error' => 'File not found on server at ' . $filePath
                  ];
              }
          }

          // Determine overall status based on deletion results
          if (empty($failedFiles) && !empty($deletedFiles)) {
              $status = 'success'; // ลบสำเร็จทั้งหมด
              $message = 'All files deleted successfully.';
          } elseif (!empty($failedFiles) && !empty($deletedFiles)) {
              $status = 'partial_success'; // ลบได้บางส่วน
              $message = 'Some files could not be deleted.';
          } else {
              $status = 'error'; // ล้มเหลวทั้งหมด หรือไม่มีไฟล์ให้ลบ
              $message = 'No files were deleted or specified.';
          }

          $response = [
              'status' => $status,
              'deleted' => $deletedFiles,
              'failed' => $failedFiles,
              'message' => $message
          ];
          http_response_code(200);

      } else {
          throw new Exception('Invalid request data. Expected \'files\' (array) and \'path\' (string).');
      }

  } catch (Exception $e) {
      $response = [
          'status' => 'error',
          'message' => $e->getMessage()
      ];
      http_response_code(400);
  }

  echo json_encode($response);
