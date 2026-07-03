# Implementation Plan: Bookmark Sync

**Branch**: `001-bookmark-sync` | **Date**: 2026-07-03 | **Spec**: specs/001-bookmark-sync/spec.md

**Input**: Feature specification from specs/001-bookmark-sync/spec.md

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/plan-template.md` for the execution workflow.

## Summary

The user's Firefox bookmarks are synced one-directionally (extension → backend) to a PHP backend
with MySQL persistence. Users register with email (as username) + password, authenticate via
the extension, and trigger sync. The backend exposes bookmarks as public browsable HTML pages
with a URL hierarchy mirroring the folder structure.

## Technical Context

**Language/Version**:
- Extension: JavaScript (Manifest V3)
- Backend: PHP 8.x

**Primary Dependencies**:
- Extension: WebExtension Bookmarks API, fetch API
- Backend: PHP framework + MySQL driver

**Storage**: MySQL

**Testing**: PHPUnit for backend API tests; manual/Playwright for extension

**Target Platform**: Firefox (extension), Linux server with PHP + MySQL (backend)

**Project Type**: Web backend + browser extension

**Performance Goals**: 500 bookmarks synced in <60s; public page load <2s for 100 bookmarks

**Constraints**: One-directional sync only; token-based auth on all API calls

**Scale/Scope**: Tens to thousands of users, each with hundreds to thousands of bookmarks

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Compliance | Notes |
|-----------|-----------|-------|
| I. Firefox Extension | ✅ | Extension is the sole client |
| II. PHP Backend API | ✅ | PHP backend with REST API |
| III. Auth API Communication | ✅ | Token-based auth on all endpoints |
| IV. Modular Independence | ✅ | extension/ and backend/ directories |
| V. API Contract First | ✅ | Contracts documented before implementation |

**GATE: PASS** — No violations. Complexity tracking not required.

## Project Structure

### Documentation (this feature)

```text
specs/001-bookmark-sync/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit.tasks)
```

### Source Code (repository root)

```text
extension/
├── manifest.json
├── background.js
├── popup/
│   ├── popup.html
│   ├── popup.css
│   └── popup.js
└── tests/

backend/
├── public/
│   └── index.php
├── src/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Middleware/
│   └── routes.php
├── config/
│   └── database.php
├── migrations/
├── views/
└── tests/

contracts/
├── auth-api.md
└── bookmarks-api.md
```

**Structure Decision**: Modular independence per constitution Principle IV.
`extension/` and `backend/` are fully independent directories with their own
dependencies and build processes. API contracts in `contracts/` bridge them.

## Complexity Tracking

*No Constitution Check violations — section not applicable.*
