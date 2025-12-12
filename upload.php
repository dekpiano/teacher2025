<?php
  // 1. Set CORS Headers
  header("Access-Control-Allow-Origin: *"); // Or specify your domain for security
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // 2. Handle Preflight Request (OPTIONS)
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      http_response_code(200);
      exit();
  }

  // Function to return a JSON error
  function return_error($message, $http_code = 500) {
      http_response_code($http_code);
      echo json_encode(["status" => "error", "message" => $message]);
      exit();
  }

  // 3. Handle POST request (for file upload)
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      
      // Check for required parameters
      if (!isset($_FILES['file']) || !isset($_POST['path'])) {
          return_error("Required parameters are missing.", 400);
      }

      // Check for upload errors
      if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
          $upload_errors = [
              UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
              UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
              UPLOAD_ERR_PARTIAL    => "The uploaded file was only partially uploaded.",
              UPLOAD_ERR_NO_FILE    => "No file was uploaded.",
              UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
              UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
              UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload.",
          ];
          $error_message = $upload_errors[$_FILES['file']['error']] ?? "Unknown upload error.";
          return_error($error_message);
      }

      $path = $_POST['path'];
      // Basic security check for the path to prevent directory traversal
      if (strpos($path, '..') !== false) {
          return_error("Invalid path specified.", 400);
      }
      
      $target_dir = __DIR__ . '/uploads/' . $path;

      // Create directory if it doesn't exist
      if (!file_exists($target_dir)) {
          if (!mkdir($target_dir, 0777, true)) {
              return_error("Failed to create directory: " . $target_dir);
          }
      }

      // Check if the directory is writable
      if (!is_writable($target_dir)) {
          return_error("The directory is not writable: " . $target_dir);
      }

      $originalName = basename($_FILES["file"]["name"]);
      // Sanitize the filename to prevent security issues
      $safeOriginalName = preg_replace("/[^a-zA-Z0-9._-]/", "", $originalName);
      if (empty($safeOriginalName)) {
          $safeOriginalName = "file";
      }
      
      $newFileName = uniqid() . '-' . $safeOriginalName;
      $target_file = $target_dir . '/' . $newFileName;

      if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
          @chmod($target_file, 0666);
          http_response_code(200);
          echo json_encode(["status" => "success", "filename" => $newFileName]);
      } else {
          // Add more context to the final error
          return_error("Sorry, there was an error moving the uploaded file. Check server logs for details.");
      }
      
      exit();
  }

  // 4. Handle other requests (e.g., GET)
  http_response_code(405); // 405 Method Not Allowed
  echo json_encode(["status" => "error", "message" => "Method Not Allowed. Please use POST to upload files."]);

?>