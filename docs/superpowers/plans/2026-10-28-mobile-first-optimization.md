# Mobile-First Optimization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

## Overview
Optimize the SKJ Teacher 2025 web system (`https://localhost:8084/`) to be fully **Mobile-First**. This transformation ensures seamless usability, touch ergonomics, responsive typography, and native-app feel across smartphones, tablets, and desktops.

## Key Changes
1. **Login Screen (`app/Views/login/index.php`)**:
   - Modern mobile-first layout using dynamic viewport heights (`100dvh`).
   - Clean, centered login card with compact touch-friendly buttons (min 48px height) on mobile.
   - Refined responsive typography and lightweight decorative elements preventing horizontal overflow or layout clipping.
   - Responsive features display (compact pill cards / 2x2 grid) so mobile teachers see the login card and key actions without endless scrolling.

2. **Main System Layout (`app/Views/teacher/layout/main.php`)**:
   - Restore backdrop overlay (`<div class="layout-overlay layout-menu-toggle"></div>`) so tapping outside the drawer menu dismisses it on mobile.
   - Responsive top navbar: adjust search bar so it never collides with the hamburger menu or user avatar on screens <= 768px.
   - **Mobile Bottom App Bar (Sticky Bottom Navigation)**:
     - Visible only on mobile/tablet screens (`d-xl-none`).
     - Quick access tabs: หน้าแรก (Home), ลงเวลา (Check-in), บันทึกเกรด (Scores), โฮมรูม (Homeroom), and เมนู (Menu drawer toggle).
     - Modern glassmorphism blur, touch feedback, safe area insets for iOS/Android home indicators.
   - Responsive breadcrumb card: enable smooth horizontal scroll on narrow viewports without multi-row awkward wrapping.
   - Global touch-friendly scrolling (`-webkit-overflow-scrolling: touch`) and spacing adjustments for mobile containers.

3. **Home Dashboard (`app/Views/teacher/home/index.php`)**:
   - Remove duplicate nested `.container-xxl` preventing double side padding on phones.
   - Mobile-first Welcome Hero Card with compact avatar, clamp typography, and ergonomic metadata pills.
   - SKJ Check-In Banner: full-width touch button on mobile with distinct status indicators.
   - Quick Stats: optimized 2-column mobile cards (`col-6`) without text truncation.
   - Action Menu Cards: responsive 2-column app grid (`col-6 col-md-4 col-lg-3`) replacing lengthy 1-column stacks.

---

## Tasks

### Task 1: Mobile-First Optimization for Login Page (`app/Views/login/index.php`)
- [ ] Review current styles and viewport meta in `app/Views/login/index.php`
- [ ] Implement mobile-first CSS rules:
  - Dynamic viewport units (`100dvh` with `100vh` fallback).
  - Responsive padding and spacing for `.login-card` and `.login-wrapper`.
  - Scaled typography for headings and buttons with touch targets >= 48px.
  - Safe bounds for background decorative blobs and floating academic elements.
  - Streamlined mobile presentation for system features list.
- [ ] Test syntax via `php -l app/Views/login/index.php`.

### Task 2: Mobile-First Layout & Bottom Navigation in `app/Views/teacher/layout/main.php`
- [ ] Restore Sneat backdrop overlay: `<div class="layout-overlay layout-menu-toggle"></div>`.
- [ ] Add Mobile Bottom Navigation Bar:
  - 5 key destinations: หน้าแรก, ลงเวลา, บันทึกเกรด, โฮมรูม, เมนู.
  - Sticky bottom with blur background, active item indicator, safe-area-inset padding.
  - Add content wrapper bottom padding (`padding-bottom: 75px` on mobile) to avoid overlaying content.
- [ ] Refactor Top Navbar for mobile viewports:
  - Adapt search box width on screens <= 768px to avoid navbar overflow.
  - Ensure avatar and toggle buttons have adequate touch clearance.
- [ ] Refactor Breadcrumb Header:
  - Ensure single-row scrollable or responsive compact breadcrumb on mobile screens.
- [ ] Test syntax via `php -l app/Views/teacher/layout/main.php`.

### Task 3: Mobile-First Dashboard Redesign in `app/Views/teacher/home/index.php`
- [ ] Remove redundant nested `.container-xxl` inside `.container-p-y`.
- [ ] Optimize Welcome Hero card for small viewports (typography, avatar, info pills).
- [ ] Optimize SKJ Check-In banner action button to full-width on mobile.
- [ ] Refactor action cards (`luxe-card`) from `col-12` to `col-6 col-md-4 col-lg-3` for a mobile app grid feel.
- [ ] Test syntax via `php -l app/Views/teacher/home/index.php`.

### Task 4: System-wide Verification & Touch/Responsive Testing
- [ ] Run PHP linter across all modified view files.
- [ ] Verify responsive breakpoints (360px, 390px, 430px, 768px, 1200px).
- [ ] Verify that desktop layout remains 100% intact and unaffected.
- [ ] Commit all changes to `feature/mobile-first-optimization`.

