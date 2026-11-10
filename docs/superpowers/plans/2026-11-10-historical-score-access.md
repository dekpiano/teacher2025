# Historical Score Access Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Let teachers view scores from periods they taught, keep historical periods read-only by default, and allow designated academic roles to grant editing for one teacher-period pair.

**Architecture:** Reuse the existing assessment dashboard and score detail view. Add one default-database grant table, role-protected academic grant controls, and a shared controller access check used by the UI and every score-writing endpoint.

**Tech Stack:** CodeIgniter 4, PHP, MySQL/MariaDB, Bootstrap.

**Spec:** `docs/superpowers/specs/2026-11-10-historical-score-access-design.md`

## Global Constraints

- Periods come only from `tb_register.RegisterYear` rows owned by the signed-in `TeacherID`.
- A prior period is read-only unless an active grant matches the exact teacher ID and period.
- Only the Measurement and Evaluation team or Deputy Director of Academic Affairs may grant/revoke access.
- Current-period behavior and reports remain unchanged.
- Historical write access must be enforced server-side, including direct AJAX requests.
- Do not add or run automated tests unless the user requests them; validate PHP and migration syntax instead.

## Review Focus

- A teacher manually posts a write request for a historical period without a grant; it must be rejected.
- A teacher alters the period/subject URL to another teacher's record; no data may be returned.
- An academic user revokes a grant while the teacher has the score page open; the next write must be rejected.
- A teacher ID or period is missing or malformed in grant actions; no grant may be created.
- No `schyear_year` entry exists; the page remains usable and does not accidentally mark all periods writable.

---

### Task 1: Store per-teacher period grants

**Files:**
- Create: `app/Database/Migrations/<timestamp>_CreateHistoricalScoreAccess.php`
- Modify: `app/Controllers/Assessment/ControllerSaveScore.php`

**Interfaces:**
- Add a private current-period resolver using the existing term/year ordering.
- Add a private predicate `canEditRegisterPeriod(string $teacherId, string $registerYear): bool` that returns true for the current period under existing policy or an active historical grant.
- Add role check for the two approved `tb_admin_rloes_academic_position` role families.

- [ ] Create the grant table with a unique `(teacher_id, register_year)` key and enabled, granted-by/time, revoked-by/time fields.
- [ ] Add helpers to read/update a grant and validate a teacher-period pair exists in `tb_register`.
- [ ] Validate the migration and changed controller PHP syntax.

### Task 2: Add teacher period selector and read-only detail mode

**Files:**
- Modify: `app/Controllers/Assessment/ControllerSaveScore.php`
- Modify: `app/Views/teacher/register/SaveScore/SaveScoreMain.php`
- Modify: `app/Views/teacher/register/SaveScore/SaveScoreAdd.php`

**Interfaces:**
- `normal()` accepts an optional `school_year` GET value, validates it against the current teacher's periods, and loads subjects for the selected period.
- `saveScoreAdd()` verifies teacher, period, and subject ownership and passes `is_read_only` to the view.

- [ ] Add a period selector sorted newest first, defaulting to the latest active period when available.
- [ ] Preserve subject and report links for the selected period.
- [ ] In historical read-only mode, show a clear locked banner, mark score fields read-only, and hide score-setting/edit controls.
- [ ] Leave report generation available.

### Task 3: Enforce historical write access and provide academic grant screen

**Files:**
- Modify: `app/Controllers/Assessment/ControllerSaveScore.php`
- Modify: `app/Config/Routes.php`
- Create: `app/Views/teacher/register/SaveScore/HistoricalAccess.php`

**Interfaces:**
- Add GET `assessment/historical-score-access` and POST `assessment/historical-score-access/update`.
- The grant screen receives distinct teacher-period pairs, teacher names, and current grant states.

- [ ] Guard `settingScore`, `insertScore`, and `autosaveScore` with `canEditRegisterPeriod`; return HTTP 403 for denied historical writes.
- [ ] Require one of the approved academic roles for both grant-screen access and POST changes; store grant/revoke actor and timestamp.
- [ ] Add a teacher/period selector and grant/revoke controls to the academic screen; reject pairs not present in teacher registration data.
- [ ] Validate PHP syntax and inspect all registered score-writing routes for an unguarded path.

### Task 4: Final review and verification

**Files:**
- Review the full diff against the approved design.

- [ ] Confirm selector values are scoped to the logged-in teacher and selected subject details are ownership-checked.
- [ ] Confirm the read-only UI and all historical write endpoints use the same grant rule.
- [ ] Confirm role aliases, grant audit fields, and migration table naming match the design.
- [ ] Run `git diff --check`, PHP lint for changed PHP files, and migration lint. Do not run automated tests unless requested.
