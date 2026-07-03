# Implementation Plan: Configure Production API URL

**Branch**: `003-configure-production-url` | **Date**: 2026-07-03 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/003-configure-production-url/spec.md`

## Summary

Update the `API_BASE` constant in both Firefox extension files (`background.js`, `popup/popup.js`) from `http://localhost:8000/api/v1` to `https://bookmarkfox.b0sh.net/api/v1`, and update the README to reflect both files.

## Technical Context

**Language/Version**: JavaScript (Manifest V3)

**Primary Dependencies**: None — plain JS/HTML/CSS extension

**Storage**: N/A (no storage changes)

**Testing**: Manual verification of API request URLs

**Target Platform**: Firefox (desktop + Android)

**Project Type**: Firefox WebExtension configuration change

**Performance Goals**: N/A (no performance impact)

**Constraints**: All API endpoint paths must be preserved — only the base URL changes

**Scale/Scope**: Single constant value change in 2 files

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Firefox Extension | ✅ | Extension structure unchanged |
| II. PHP Backend API | ✅ | No backend changes |
| III. Authenticated REST API Communication | ✅ | Only base URL changes, auth scheme preserved |
| IV. Modular Independence | ✅ | Extension-only change, no backend coupling |
| V. API Contract First | ✅ | API contract unchanged — same endpoints, same shapes |
| VI. Module-Level Documentation | ✅ | README updated to reflect new URL and both files |

**Gate verdict**: ✅ PASS — all principles satisfied. No violations to justify.

## Project Structure

### Documentation (this feature)

```text
specs/003-configure-production-url/
├── plan.md              # This file
├── research.md          # N/A — no unknowns to research
├── data-model.md        # N/A — no data model changes
├── quickstart.md        # Validation guide
├── contracts/           # N/A — no API contract changes
└── tasks.md             # Phase 2 output
```

### Source Code (repository root)

```text
extension/
├── background.js        # Updated API_BASE
├── popup/popup.js       # Updated API_BASE
├── README.md            # Updated config docs
└── ... (unchanged)
```

**Structure Decision**: Single-project (extension only). No backend, contracts, or infrastructure changes needed.

## Complexity Tracking

No constitution violations — complexity tracking not required.
