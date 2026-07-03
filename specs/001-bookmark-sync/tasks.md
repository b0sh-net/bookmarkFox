---

description: "Task list for bookmark sync feature implementation"

---

# Tasks: Bookmark Sync

**Input**: Design documents from specs/001-bookmark-sync/

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are OPTIONAL — not requested in spec, so none are generated.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Backend**: `backend/public/`, `backend/src/`, `backend/tests/`
- **Extension**: `extension/`
- **Contracts**: `contracts/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic directory structure

- [x] T001 Create backend/ directory structure (public, src/Controllers, src/Models, src/Services, src/Middleware, config, migrations, views, tests)
- [x] T002 Initialize Laravel project in backend/ with Composer and configure Sanctum
- [x] T003 Create extension/ directory structure (popup/, tests/)
- [x] T004 [P] Create extension/manifest.json with Manifest V3 structure and bookmarks/storage permissions
- [x] T005 [P] Configure backend/.env with MySQL connection settings and app key generation

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T006 Setup MySQL migrations for users, bookmark_folders, bookmarks tables in backend/migrations/
- [x] T007 [P] Install and configure Laravel Sanctum in backend/config/sanctum.php
- [x] T008 [P] Create API route groups with auth middleware in backend/src/routes.php
- [x] T009 Create base AppController with JSON response helpers in backend/src/Controllers/AppController.php
- [x] T010 Configure validation error handling in backend/src/Exceptions/Handler.php

**Checkpoint**: Foundation ready — user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - User Registration and Authentication (Priority: P1) 🎯 MVP

**Goal**: Users can register with email + password and authenticate to receive an API token.

**Independent Test**: Submit registration via `POST /api/v1/register` with email + password, receive token. Then login with same credentials, receive token. Invalid credentials return 401.

### Implementation for User Story 1

- [x] T011 [P] [US1] Create User model with HasApiTokens trait and fillable fields in backend/src/Models/User.php
- [x] T012 [P] [US1] Create AuthController with register, login, logout, me actions in backend/src/Controllers/AuthController.php
- [x] T013 [P] [US1] Create AuthService with register and login logic in backend/src/Services/AuthService.php
- [x] T014 [US1] Wire auth routes (register, login, logout, me) in backend/src/routes.php
- [x] T015 [US1] Create extension popup HTML with login form (email + password fields + submit button) in extension/popup/popup.html
- [x] T016 [US1] Create extension popup CSS with modern styling in extension/popup/popup.css
- [x] T017 [US1] Create extension popup JS with login/logout API calls and token storage in extension/popup/popup.js
- [x] T018 [US1] Create extension background script with auth state management in extension/background.js

**Checkpoint**: At this point, User Story 1 should be fully functional — user can register, login, and receive/store a token.

---

## Phase 4: User Story 2 - Bookmark Sync from Extension (Priority: P1)

**Goal**: Authenticated users can sync their Firefox bookmarks one-directionally to the backend.

**Independent Test**: Authenticate via extension, click sync, verify bookmarks appear in MySQL database.

### Implementation for User Story 2

- [x] T019 [P] [US2] Create BookmarkFolder model with relationships (user, parent, children) in backend/src/Models/BookmarkFolder.php
- [x] T020 [P] [US2] Create Bookmark model with relationships (user, folder) in backend/src/Models/Bookmark.php
- [x] T021 [US2] Create BookmarkSyncController with sync action in backend/src/Controllers/BookmarkSyncController.php
- [x] T022 [US2] Implement SyncService with tree parsing, diff computation, and upsert/deletion logic in backend/src/Services/SyncService.php
- [x] T023 [US2] Wire sync route (POST /api/v1/bookmarks/sync) in backend/src/routes.php
- [x] T024 [US2] Implement bookmark tree reader using browser.bookmarks.getTree() in extension/background.js
- [x] T025 [US2] Implement sync API caller (POST /api/v1/bookmarks/sync) in extension/background.js
- [x] T026 [US2] Add sync button, status indicator, and last-sync timestamp to extension popup UI in extension/popup/popup.html and extension/popup/popup.js

**Checkpoint**: At this point, User Stories 1 AND 2 should both work — bookmarks flow from Firefox to the backend.

---

## Phase 5: User Story 3 - Public Bookmark Browsing Pages (Priority: P2)

**Goal**: Visitors can browse any user's bookmarks via public HTML pages with hierarchical URLs.

**Independent Test**: Navigate to `http://localhost:8000/{email}` — see top-level folders. Navigate to `{email}/{folder}` — see folder contents with bookmarks and subfolders.

### Implementation for User Story 3

- [x] T027 [P] [US3] Create PublicPageController with user root, folder, and subfolder actions in backend/src/Controllers/PublicPageController.php
- [x] T028 [P] [US3] Create responsive Blade layout with Tailwind CSS in backend/resources/views/layouts/public.blade.php
- [x] T029 [US3] Create user root page view showing top-level folders and bookmarks in backend/resources/views/public/root.blade.php
- [x] T030 [US3] Create folder page view showing folder contents in backend/resources/views/public/folder.blade.php
- [x] T031 [US3] Wire public routes (GET /{email}, GET /{email}/{folder}, GET /{email}/{folder}/{subfolder}) in backend/src/routes.php
- [x] T032 [US3] Add 404 user/folder not found views in backend/resources/views/errors/

**Checkpoint**: All user stories should now be independently functional.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T033 [P] Add rate limiting on auth endpoints (register, login) in backend/src/routes.php
- [x] T034 Add request validation (email format, password min length, bookmark URL sanitization) in backend/src/
- [x] T035 Run quickstart.md validation scenarios end-to-end

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion — BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User stories can then proceed in parallel (if staffed)
  - Or sequentially in priority order (P1 → P1 → P2)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) — No dependencies on other stories
- **User Story 2 (P1)**: Can start after Foundational (Phase 2) — Depends on US1 (needs auth token)
- **User Story 3 (P2)**: Can start after Foundational (Phase 2) — Depends on US2 (needs synced data)

### Within Each User Story

- Models before services
- Services before controllers
- Controllers before routes
- Backend before extension (for API-dependent features)
- Story complete before moving to next priority

### Parallel Opportunities

- All Setup tasks marked [P] can run in parallel
- All Foundational tasks marked [P] can run in parallel (within Phase 2)
- Models within each story marked [P] can run in parallel
- Different user stories can be worked on in parallel by different team members

---

## Parallel Example: User Story 1

```bash
# Launch models for US1 in parallel:
Task: "Create User model with HasApiTokens trait in backend/src/Models/User.php"
Task: "Create AuthController with register, login, logout, me actions in backend/src/Controllers/AuthController.php"
Task: "Create AuthService with register and login logic in backend/src/Services/AuthService.php"

# After models are done, launch UI tasks:
Task: "Create extension popup HTML with login form in extension/popup/popup.html"
Task: "Create extension popup CSS with modern styling in extension/popup/popup.css"
Task: "Create extension popup JS with login/logout API calls in extension/popup/popup.js"
Task: "Create extension background script with auth state in extension/background.js"
```

## Parallel Example: User Story 2

```bash
# Launch models for US2 in parallel:
Task: "Create BookmarkFolder model in backend/src/Models/BookmarkFolder.php"
Task: "Create Bookmark model in backend/src/Models/Bookmark.php"
```

## Parallel Example: User Story 3

```bash
# Launch controller and layout together:
Task: "Create PublicPageController in backend/src/Controllers/PublicPageController.php"
Task: "Create responsive Blade layout in backend/resources/views/layouts/public.blade.php"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational
3. Complete Phase 3: User Story 1 (Registration + Auth)
4. **STOP and VALIDATE**: Test registration and login end-to-end
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP!)
3. Add User Story 2 → Test independently → Deploy/Demo
4. Add User Story 3 → Test independently → Deploy/Demo
5. Each story adds value without breaking previous stories

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational is done:
   - Developer A: User Story 1 (Registration/Auth)
   - Developer B: User Story 2 (Bookmark Sync) — can start after US1
   - Developer C: User Story 3 (Public Pages) — can start after US2
3. Stories complete and integrate independently

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence

---

## Phase 7: Convergence

**Purpose**: Close gaps identified by `/speckit.converge` between documented intent and current codebase.

- [x] T036 Configure Laravel view path to resolve `backend/views/` (create `config/view.php` pointing to `../views/` or add ViewServiceProvider) per FR-008, FR-010 (partial)
- [x] T037 Add periodic background sync in extension using `chrome.alarms` API (sync every 5 minutes while authenticated) per spec Assumptions (missing)
- [x] T038 Add pagination (e.g., 50 items per page) to PublicPageController and Blade views to handle large bookmark sets per spec Edge Cases (partial)
- [x] T039 Add URL validation in SyncService to filter/skip non-HTTP/HTTPS bookmark URLs per spec Edge Cases (missing)
