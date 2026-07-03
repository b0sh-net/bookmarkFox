# Tasks: Configure Production API URL

**Input**: Design documents from `specs/003-configure-production-url/`

**Prerequisites**: plan.md, spec.md

**Tests**: Manual verification only (no test framework exists for this extension)

**Organization**: Single user story — this is a pure configuration change.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1)
- Include exact file paths in descriptions

## Path Conventions

- Extension files under `extension/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Verify current state and identify files to change

- [x] T001 Read current API_BASE values in extension/background.js and extension/popup/popup.js
- [x] T002 Read extension/README.md Configuration section for current docs

---

## Phase 2: Configuration Change

**Purpose**: Update API_BASE from localhost to production

- [x] T003 [P] [US1] Update API_BASE in extension/background.js from `http://localhost:8000/api/v1` to `https://bookmarkfox.b0sh.net/api/v1`
- [x] T004 [P] [US1] Update API_BASE in extension/popup/popup.js from `http://localhost:8000/api/v1` to `https://bookmarkfox.b0sh.net/api/v1`
- [x] T005 [US1] Update extension/README.md Configuration section to reflect new URL and mention both files

**Checkpoint**: All API requests now point to production.

---

## Phase 3: Validation

**Purpose**: Verify the change is correct and complete

- [x] T006 [US1] Verify no remaining references to `localhost:8000` in extension/ files
- [x] T007 [US1] Confirm API endpoint paths are preserved (no accidental changes to relative paths after API_BASE)
- [x] T008 [US1] Run quickstart.md validation guide

**Checkpoint**: Feature complete — extension points to production API.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Configuration Change (Phase 2)**: Depends on Setup completion
- **Validation (Phase 3)**: Depends on Phase 2 completion

### Parallel Opportunities

- T003 and T004 can run in parallel (different files)

---

## Parallel Example

```bash
# Launch both file updates together:
Task: "Update API_BASE in extension/background.js"
Task: "Update API_BASE in extension/popup/popup.js"
```

---

## Implementation Strategy

### MVP

1. Complete Phase 1: Setup (identify files)
2. Complete Phase 2: Configuration Change (update 3 files)
3. Complete Phase 3: Validation

Single iteration — no incremental delivery needed for a 3-file config change.
