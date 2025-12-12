<?php
  // 1. ตั้งค่า CORS Headers
  header("Access-Control-Allow-Origin: *"); // หรือระบุโดเมนของคุณเพื่อความปลอดภัย
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // 2. จัดการกับ Preflight Request (OPTIONS)
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      http_response_code(200);
      exit();
  }

  // 3. จัดการกับคำขอ POST (สำหรับอัปโหลดไฟล์)
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // โค้ดอัปโหลดไฟล์ของคุณจะอยู่ที่นี่
      // ...
      // ตัวอย่าง:
      if (isset($_FILES['file']) && isset($_POST['path'])) {
          $path = $_POST['path'];
          $target_dir = __DIR__ . '/uploads/' . $path;

          if (!file_exists($target_dir)) {
              mkdir($target_dir, 0777, true);
          }

          $originalName = basename($_FILES["file"]["name"]);
          $newFileName = uniqid() . '-' . $originalName;
          $target_file = $target_dir . '/' . $newFileName;

          if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                @chmod($target_file, 0666);
              http_response_code(200);
              echo json_encode(["status" => "success", "filename" => $newFileName]);
          } else {
              http_response_code(500);
              echo json_encode(["status" => "error", "message" => "Sorry, there was an error uploading your file."]);
          }
      } else {
          http_response_code(400);
          echo json_encode(["status" => "error", "message" => "Required parameters are missing."]);
      }
      exit();
  }

  // 4. จัดการกับคำขออื่นๆ (เช่น GET)
  // ถ้ามีคนเข้าถึง upload.php โดยตรงผ่านเบราว์เซอร์
  http_response_code(405); // 405 Method Not Allowed
  echo json_encode(["status" => "error", "message" => "Method Not Allowed. Please use POST to upload files."]);

  ?>