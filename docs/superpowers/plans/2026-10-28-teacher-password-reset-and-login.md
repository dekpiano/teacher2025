# Teacher Password Reset & Dual-Mode Login Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

## Overview
Transform the Teacher Password Reset feature (`/verify-email`) and Login system (`/login`) in `teacher2025` from a database-only placeholder into a fully operational, live Google Workspace-synced identity management system with dual-mode login capabilities.

## Problem Statement
1. **Google Workspace Sync Missing**: Teachers who reset their password receive a generated password (`Skj@xxxxxx`), but it is only updated in `tb_personnel` (MySQL). Google Workspace Directory API is never invoked because `GoogleWorkspaceService.php` and `google_service_account.json` are missing. As a result, teachers cannot sign into Gmail or Google OAuth with the new password.
2. **Missing Direct Login Form on `/login`**: The login page only has a Google SSO button (`เข้าสู่ระบบด้วย @skj.ac.th`). Even if a teacher's password is saved in MySQL, there is no form to log in using username and password.
3. **Position Display Bug**: In `/verify-email`, teacher position displays as a raw integer ID (e.g., `1`) rather than the Thai title (e.g., `ครูผู้ช่วย`, `ครู ค.ศ.1`).
4. **Dead Route**: `Routes.php` contains an unmapped route `verify-email/check`.

## Solution Architecture
1. **Lightweight Google Workspace Directory API Service (`app/Libraries/GoogleWorkspaceService.php`)**:
   - Zero-external-dependency Google Service Account client using native PHP OpenSSL (`openssl_sign` with `OPENSSL_ALGO_SHA256`) and CodeIgniter 4 `CURLRequest`.
   - Exchanges RSA256-signed JWT for Google OAuth2 Bearer token at `https://oauth2.googleapis.com/token` impersonating `dekpiano@skj.ac.th` with scope `https://www.googleapis.com/auth/admin.directory.user`.
   - Issues live `PATCH` to `https://admin.googleapis.com/admin/directory/v1/users/{email}` updating user password in Google Workspace.
   - Comprehensive error translation to user-friendly Thai descriptions.
2. **Credentials Setup & Security**:
   - Copy school's authorized `google_service_account.json` to `keys/` and `writable/keys/`.
   - Update `.gitignore` to safeguard credentials.
3. **Controller Enhancement (`app/Controllers/ControlTeacherVerification.php`)**:
   - JOIN `skjacth_skj.tb_position` to retrieve `posi_name`.
   - Verify Google API password update succeeds before updating MySQL and returning success.
   - Remove dead route in `app/Config/Routes.php`.
4. **Dual-Mode Login on `/login` (`app/Views/login/index.php`)**:
   - Primary: Google Workspace OAuth SSO button.
   - Alternative: Collapsible/Tabbed Direct Login form using Username + Password connected to `Login::authenticate()`.
5. **UI & UX Refinement (`app/Views/EmailVerification/teacher_verify_index.php`)**:
   - Display verified teacher position title and Google Workspace sync badge.

---

## Tasks

### Task 1: Security Configuration & Service Account Key Deployment
- [ ] Add `google_service_account.json`, `keys/`, and `writable/keys/` to `.gitignore`.
- [ ] Copy `google_service_account.json` from `D:\SkjSystem\student2025\google_service_account.json` to `keys/google_service_account.json` and `writable/keys/google_service_account.json`.
- [ ] Verify file existence and readability.

### Task 2: Implement Lightweight Google Workspace Service (`app/Libraries/GoogleWorkspaceService.php`)
- [ ] Create `app/Libraries/GoogleWorkspaceService.php`:
  - Configuration detection for key locations (`keys/`, `writable/keys/`, root).
  - OpenSSL RS256 JWT builder with domain-wide delegation for `dekpiano@skj.ac.th`.
  - Bearer token exchange against `https://oauth2.googleapis.com/token`.
  - Directory API `updateUserPassword(string $email, string $newPassword)` via cURL.
  - Thai error messages for common Google API scenarios (404 Not Found, 403 Forbidden, 400 Invalid Password).
- [ ] Test the service with a standalone validation script to verify JWT signing and Google API connectivity.

### Task 3: Update Teacher Verification Controller & Routes
- [ ] Edit `app/Controllers/ControlTeacherVerification.php`:
  - JOIN `tb_position` from database `skj` on `tb_position.posi_id = tb_personnel.pers_position`.
  - Instantiate `GoogleWorkspaceService` and call `updateUserPassword($email, $newPassword)`.
  - Handle Google API failures gracefully: return `status = 0` with descriptive error when Google sync fails.
  - Only update `pers_password` in `tb_personnel` when Google sync succeeds.
  - Output human-readable position name in response JSON.
- [ ] Clean up route in `app/Config/Routes.php` (remove dead route `verify-email/check`).

### Task 4: Enhance Verification UI (`app/Views/EmailVerification/teacher_verify_index.php`)
- [ ] Update result box in `teacher_verify_index.php`:
  - Add Google Workspace status pill/badge indicating live synchronization.
  - Ensure position name renders smoothly.
  - Keep responsive styling consistent with Sneat theme.

### Task 5: Add Dual-Mode Login to `/login` (`app/Views/login/index.php`)
- [ ] Add direct Username & Password login form on `app/Views/login/index.php`:
  - Clean tab/toggle between "เข้าสู่ระบบด้วย @skj.ac.th" (Google) and "เข้าสู่ระบบด้วยรหัสผ่าน" (Direct).
  - Include username and password fields with toggle password visibility button.
  - AJAX submission to `<?= base_url('login/authenticate') ?>`.
  - Loading spinner, SweetAlert/inline error display, and redirect to `home` on success.
- [ ] Verify Sneat styling consistency and mobile responsiveness.

### Task 6: End-to-End Verification
- [ ] Run syntax check on all modified PHP files (`app/Libraries/GoogleWorkspaceService.php`, `app/Controllers/ControlTeacherVerification.php`, `app/Views/EmailVerification/teacher_verify_index.php`, `app/Views/login/index.php`).
- [ ] Verify reset password endpoint logic with valid and invalid teacher test data.
- [ ] Confirm direct login endpoint logic.

