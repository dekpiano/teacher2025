# Historical Score Access Design

## Intent

Let a teacher select academic periods in which they were assigned subjects, review the saved scores, and keep prior periods read-only by default. The academic team can grant or revoke editing for one teacher and one period at a time.

## Goals

- Show a period selector containing only `RegisterYear` values associated with the signed-in teacher's `TeacherID`.
- Keep the current period on the existing score-entry flow and policies.
- Display historical scores and reports without editing controls unless an active grant exists for that teacher and period.
- Give only the Measurement and Evaluation team (`งานวัดและประเมินผล`, with `งานวัดผล` alias) and the Deputy Director of Academic Affairs (`รองผู้อำนวยการฝ่ายวิชาการ`, with `รองวิชาการ` alias) the ability to grant/revoke historical editing.
- Enforce the same access rule on all score-writing endpoints, even if a teacher manually submits a request.
- Record who granted/revoked access and when.

## Non-goals

- Do not grant access to another teacher's subjects or records.
- Do not change score calculation, report output, or current-period open/close rules.
- Do not allow teachers to grant access to themselves.

## Anti-goals

- Never treat a hidden/disabled input as sufficient protection for historical records.
- Never make a grant global across all teachers for a school year.

## Approaches

1. **Add a year selector to the existing teacher page and a separate academic grant screen (selected).** Reuse the existing subject/detail views, default past periods to read-only, and create a per-teacher/per-period grant record. This keeps teacher workflows familiar while placing approval controls behind a dedicated role check.
2. **Create a separate read-only archive and leave the score-entry view untouched.** This isolates the UI, but duplicates the existing subject and student views and still requires write-endpoint protection and an academic grant surface.

Approach 1 is selected because it reuses the current routes and data presentation while allowing both interface and server-side enforcement to share one teacher-period permission.

## Constraints and behavior

- The current period is the latest `schyear_year` selected by the existing term/year sort. Any `RegisterYear` other than that period is historical.
- Selecting a period reloads the existing subject list for that teacher and period. Subject detail queries must verify both the teacher ID and the requested period/subject.
- A historical period is read-only unless an active grant exists for the signed-in teacher ID and exact `RegisterYear`.
- The grant screen lists teacher-period pairs backed by registered subjects. Grant/revoke actions require a current matching `tb_admin_rloes` role; authorization is checked server-side on every action.
- A new default-database table stores one current grant state per teacher-period pair with grant/revoke actor and timestamp fields. A CodeIgniter migration creates the table.
- Score setting, insert, update, and autosave endpoints must reject historical writes without a grant. Read endpoints and report generation remain available to the assigned teacher.
- Revoking a grant takes effect immediately, including for an already-open score page.

## Implementation ownership

- `ControllerSaveScore`: resolve teacher periods and current period; validate teacher/subject ownership; compute read-only access; enforce it on write endpoints; serve and process the role-protected grant screen.
- `SaveScoreMain.php` and `SaveScoreAdd.php`: period selector and clear read-only indicators/controls.
- `Routes.php`: add grant-screen and grant/revoke routes.
- Migration: create the per-teacher/per-period grant table with audit fields and a unique key.
- New grant view: allow authorized academic roles to inspect and change grants.

## Verification

- Review the diff for all score-writing routes and confirm every path checks the same period access rule.
- Validate PHP syntax and migration syntax after implementation. No automated tests are added or run unless requested.
