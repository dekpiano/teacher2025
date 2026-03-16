<?php
  header("Access-Control-Allow-Origin: *"); 
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      exit();
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $path = trim($_POST['path'] ?? 'temp', '/');
      $filename = $_POST['filename'] ?? ($_FILES['file']['name'] ?? 'file_' . uniqid()); 
      $chunkIndex = isset($_POST['chunk']) ? intval($_POST['chunk']) : 0;
      $totalChunks = isset($_POST['chunks']) ? intval($_POST['chunks']) : 1;
      
      $target_dir = __DIR__ . '/uploads/' . $path;
      if (!file_exists($target_dir)) {
          @mkdir($target_dir, 0777, true);
      }

      $target_file = $target_dir . '/' . $filename;
      
      /**
       * Heart of Chunking:
       * If it's the first chunk (0), write fresh (wb).
       * If it's subsequent chunks, append to the SAME file (ab).
       */
      $mode = ($chunkIndex === 0) ? 'wb' : 'ab';
      $out = @fopen($target_file, $mode);
      $in = @fopen($_FILES['file']['tmp_name'], 'rb');
      
      if ($out && $in) {
          while ($buff = fread($in, 4096)) {
              fwrite($out, $buff);
          }
          fclose($in);
          fclose($out);
          
          if ($chunkIndex + 1 === $totalChunks) {
              @chmod($target_file, 0666);
              echo json_encode(["status" => "success", "filename" => $filename, "full" => true]);
          } else {
              echo json_encode(["status" => "success", "chunk" => $chunkIndex, "full" => false]);
          }
      } else {
          http_response_code(500);
          echo json_encode(["status" => "error", "message" => "Failed to write file parts at chunk: " . $chunkIndex]);
      }
      exit();
  }
?>