<?php

namespace App\Libraries;

/**
 * GoogleWorkspaceService
 *
 * A lightweight, zero-external-dependency Google Workspace Directory API client.
 * Uses native PHP OpenSSL (RS256 JWT) and CodeIgniter 4 CURLRequest
 * to authenticate with Google Service Account Domain-Wide Delegation.
 */
class GoogleWorkspaceService
{
    protected string $adminEmail = 'dekpiano@skj.ac.th';
    protected ?string $keyFilePath = null;
    protected ?string $clientEmail = null;
    protected ?string $privateKey = null;
    protected bool $isConfigured = false;
    protected ?string $lastError = null;

    // Cache token in memory for execution lifecycle
    protected static ?string $cachedAccessToken = null;
    protected static int $tokenExpiresAt = 0;

    public function __construct()
    {
        $envAdmin = getenv('GOOGLE_ADMIN_EMAIL') ?: ((function_exists('env') ? env('GOOGLE_ADMIN_EMAIL') : null) ?: 'dekpiano@skj.ac.th');
        $this->adminEmail = $envAdmin;

        $this->detectAndLoadKey();
    }

    /**
     * Locate and validate service account key file
     */
    protected function detectAndLoadKey(): void
    {
        $possiblePaths = [
            ROOTPATH . 'keys/google_service_account.json',
            WRITEPATH . 'keys/google_service_account.json',
            ROOTPATH . 'google_service_account.json',
            APPPATH . 'Config/google_service_account.json',
            WRITEPATH . 'google_service_account.json',
        ];

        foreach ($possiblePaths as $path) {
            if (is_file($path)) {
                $this->keyFilePath = $path;
                break;
            }
        }

        if (!$this->keyFilePath) {
            $this->lastError = 'ไม่พบไฟล์คีย์ Service Account (google_service_account.json)';
            $this->isConfigured = false;
            return;
        }

        try {
            $jsonContent = json_decode(file_get_contents($this->keyFilePath), true);
            if (!$jsonContent || empty($jsonContent['client_email']) || empty($jsonContent['private_key'])) {
                $this->lastError = 'ไฟล์ google_service_account.json ไม่ถูกต้องหรือขาด client_email / private_key';
                $this->isConfigured = false;
                return;
            }

            $this->clientEmail = $jsonContent['client_email'];
            $this->privateKey = $jsonContent['private_key'];
            $this->isConfigured = true;
        } catch (\Throwable $e) {
            $this->lastError = 'อ่านไฟล์ Service Account ล้มเหลว: ' . $e->getMessage();
            $this->isConfigured = false;
        }
    }

    public function isConfigured(): bool
    {
        return $this->isConfigured;
    }

    public function getKeyFilePath(): ?string
    {
        return $this->keyFilePath;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Get or fetch Google OAuth2 Bearer Access Token using RS256 JWT
     */
    protected function getAccessToken(): ?string
    {
        if (self::$cachedAccessToken && time() < (self::$tokenExpiresAt - 60)) {
            return self::$cachedAccessToken;
        }

        if (!$this->isConfigured || empty($this->privateKey) || empty($this->clientEmail)) {
            $this->lastError = 'Service Account ยังไม่ได้ตั้งค่าอย่างสมบูรณ์';
            return null;
        }

        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claim = [
            'iss' => $this->clientEmail,
            'sub' => $this->adminEmail,
            'scope' => 'https://www.googleapis.com/auth/admin.directory.user',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $claimEncoded = $this->base64UrlEncode(json_encode($claim, JSON_UNESCAPED_SLASHES));
        $dataToSign = $headerEncoded . '.' . $claimEncoded;

        $signature = '';
        $signSuccess = openssl_sign($dataToSign, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        if (!$signSuccess) {
            $this->lastError = 'การสร้างลายเซ็นดิจิทัล RSA-SHA256 ล้มเหลว: ' . openssl_error_string();
            return null;
        }

        $jwt = $dataToSign . '.' . $this->base64UrlEncode($signature);

        // Exchange JWT for Access Token
        $curl = \Config\Services::curlrequest();
        try {
            $response = $curl->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
                'timeout' => 15,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 && !empty($body['access_token'])) {
                self::$cachedAccessToken = $body['access_token'];
                self::$tokenExpiresAt = $now + (int) ($body['expires_in'] ?? 3600);
                return self::$cachedAccessToken;
            }

            $rawErr = $body['error_description'] ?? ($body['error'] ?? 'HTTP ' . $statusCode);
            $this->lastError = 'ไม่สามารถขอรับสิทธิ์จาก Google ได้: ' . $this->formatGoogleErrorMessage((string) $rawErr);
            return null;
        } catch (\Throwable $e) {
            $this->lastError = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์ Google: ' . $e->getMessage();
            return null;
        }
    }

    /**
     * Reset / Update user password in Google Workspace Directory API
     *
     * @param string $email Teacher Google email (e.g. teacher@skj.ac.th)
     * @param string $newPassword New plain text password
     * @param bool $forceChangeNextLogin Whether user must change password at next login
     * @return array ['success' => bool, 'message' => string, 'user_id' => ?string]
     */
    public function updateUserPassword(string $email, string $newPassword, bool $forceChangeNextLogin = false): array
    {
        if (!$this->isConfigured) {
            return [
                'success' => false,
                'message' => 'ระบบยังไม่พบคีย์ Google Service Account (google_service_account.json)',
            ];
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => $this->lastError ?: 'ไม่สามารถยืนยันตัวตนกับ Google API ได้',
            ];
        }

        $curl = \Config\Services::curlrequest();
        $targetUrl = 'https://admin.googleapis.com/admin/directory/v1/users/' . urlencode($email);

        try {
            $payload = [
                'password' => $newPassword,
                'changePasswordAtNextLogin' => $forceChangeNextLogin,
            ];

            $response = $curl->request('PATCH', $targetUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($payload),
                'timeout' => 15,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'success' => true,
                    'message' => 'รีเซ็ตรหัสผ่านในระบบ Google Workspace สำเร็จเรียบร้อย!',
                    'user_id' => $body['id'] ?? null,
                ];
            }

            $rawError = $body['error']['message'] ?? ($body['error_description'] ?? ('HTTP ' . $statusCode));
            return [
                'success' => false,
                'message' => 'Google API รายงานข้อผิดพลาด: ' . $this->formatGoogleErrorMessage((string) $rawError),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อเพื่ออัปเดตรหัสผ่าน: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Friendly Thai error descriptions
     */
    protected function formatGoogleErrorMessage(string $rawError): string
    {
        if (strpos($rawError, 'Resource Not Found') !== false || strpos($rawError, '404') !== false) {
            return 'ไม่พบบัญชีอีเมลนี้ในระบบ Google Workspace (@skj.ac.th)';
        }
        if (strpos($rawError, 'invalid_grant') !== false || strpos($rawError, 'Invalid signature') !== false) {
            return 'กุญแจเชื่อมต่อ Google API (Service Account Key) ไม่ถูกต้องหรือถูกเพิกถอน';
        }
        if (strpos($rawError, 'unauthorized_client') !== false) {
            return 'Service Account ยังไม่ได้รับสิทธิ์ Domain-Wide Delegation ใน Google Workspace Admin Console';
        }
        if (strpos($rawError, 'access_denied') !== false || strpos($rawError, '403') !== false) {
            return 'ไม่มีสิทธิ์เข้าถึงหรือจัดการบัญชีผู้ใช้ใน Google Workspace (Error 403: Forbidden)';
        }
        if (strpos($rawError, 'Password is weak') !== false || strpos($rawError, 'Password does not meet') !== false) {
            return 'รหัสผ่านใหม่ไม่ตรงตามนโยบายความปลอดภัยของ Google Workspace';
        }
        return $rawError;
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}