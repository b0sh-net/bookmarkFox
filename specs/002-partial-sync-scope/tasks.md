---

description: "Task list for partial sync scope feature implementation"

---

# Tasks: Partial Sync Scope

**Input**: Design documents from specs/002-partial-sync-scope/

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, quickstart.md

**Tests**: Tests are OPTIONAL — not requested in spec, so none are generated.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2)
- Include exact file paths in descriptions

## Path Conventions

- **Extension**: `extension/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: No setup tasks needed — extension is already initialized from the
first feature. This feature only modifies existing files.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: No foundational tasks — this feature has no backend or
infrastructure changes.

---

## Phase 3: User Story 1 - Choose Sync Root Folder (Priority: P1) 🎯 MVP

**Goal**: Users can select a top-level bookmark folder as the sync root from
the extension popup. Only that folder's subtree is sent to the backend.

**Independent Test**: Open popup, see folder picker, select a folder, run sync,
verify only that folder's bookmarks appear on the backend.

### Implementation for User Story 1

- [x] T001 [P] [US1] Add folder picker dropdown to popup HTML in extension/popup/popup.html
- [x] T002 [P] [US1] Add folder picker styles to extension/popup/popup.css
- [x] T003 [US1] Implement folder enumeration (get top-level folders via bookmarks.getTree) and folder picker population in extension/popup/popup.js
- [x] T004 [US1] Implement sync root selection handler (save selected folder ID and title to chrome.storage.local) in extension/popup/popup.js
- [x] T005 [US1] Implement clear selection handler (remove sync root from storage, restore full-sync mode) in extension/popup/popup.js
- [x] T006 [US1] Implement tree pruning logic (filter tree to selected folder's subtree by firefox_id) in extension/background.js
- [x] T007 [US1] Wire tree pruning into sync flow: read syncRootId from storage before getTree, prune if set in extension/background.js

**Checkpoint**: At this point, User Story 1 should be fully functional — user can select a folder and sync only its subtree.

---

## Phase 4: User Story 2 - Sync Root Persistence (Priority: P1)

**Goal**: The extension remembers the selected sync root across browser restarts.

**Independent Test**: Select a sync root, close Firefox, reopen, open popup,
verify the same folder is still selected.

### Implementation for User Story 2

- [x] T008 [US2] Load syncRootId and syncRootTitle from chrome.storage.local on popup open and pre-select the folder in the picker in extension/popup/popup.js
- [x] T009 [US2] Implement deleted-folder fallback: read syncRootId before sync, verify folder exists in tree, clear storage and notify user if missing in extension/background.js

**Checkpoint**: Sync root persists across restarts; deleted folders trigger graceful fallback.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Final validation

- [x] T010 Run quickstart.md validation scenarios end-to-end

---

## Dependencies & Execution Order

### Phase Dependencies

- **US1 (Phase 3)**: Can start immediately — no dependencies
- **US2 (Phase 4)**: Depends on US1 (needs folder picker and storage in place)
- **Polish (Phase 5)**: Depends on all user stories

### Within Each User Story

- UI before logic (HTML/CSS before JS)
- Storage before tree pruning

### Parallel Opportunities

- T001 and T002 (HTML + CSS) can run in parallel

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 3: User Story 1 (folder picker + tree pruning)
2. **STOP and VALIDATE**: Test folder selection and filtered sync

### Incremental Delivery

1. Add User Story 1 → Test independently → Deploy
2. Add User Story 2 → Test independently → Deploy

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- All changes are extension-only; no backend modifications
