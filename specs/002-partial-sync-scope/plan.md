# Implementation Plan: Partial Sync Scope

**Branch**: `002-partial-sync-scope` | **Date**: 2026-07-03 | **Spec**: specs/002-partial-sync-scope/spec.md

**Input**: Feature specification from specs/002-partial-sync-scope/spec.md

**Note**: This template is filled in by the `/speckit.plan` command.

## Summary

Users can select a top-level bookmark folder as the sync root in the extension
popup. Only bookmarks under that folder are sent to the backend. The selection
persists across browser restarts. Filtering happens entirely in the extension.

## Technical Context

**Language/Version**: JavaScript (Manifest V3)

**Primary Dependencies**: WebExtension Bookmarks API, storage.local API

**Storage**: chrome.storage.local (sync root folder ID)

**Testing**: Manual testing via Firefox about:debugging

**Target Platform**: Firefox (extension only — no backend changes)

**Project Type**: Browser extension feature enhancement

**Performance Goals**: Folder picker loads in <1s; tree pruning adds <100ms to sync

**Constraints**: No backend changes; filtering occurs client-side before API call

**Scale/Scope**: Single user selecting from dozens of top-level folders

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Compliance | Notes |
|-----------|-----------|-------|
| I. Firefox Extension | ✅ | Feature lives entirely in extension |
| III. Auth API Communication | ✅ | Auth flow unchanged |
| IV. Modular Independence | ✅ | No backend changes needed |
| VI. Module-Level Documentation | ✅ | README will reflect new feature |

**GATE: PASS** — No violations. Complexity tracking not required.

## Project Structure

### Documentation (this feature)

```text
specs/002-partial-sync-scope/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit.tasks)
```

### Source Code (repository root)

```text
extension/
├── background.js        # Add tree pruning by sync root
├── popup/
│   ├── popup.html       # Add folder picker UI
│   ├── popup.css        # Add folder picker styles
│   └── popup.js         # Add folder selection + persistence logic
```

**Structure Decision**: Extension-only changes. No new files needed — existing
files (`background.js`, `popup.html`, `popup.css`, `popup.js`) are modified.

## Complexity Tracking

*No Constitution Check violations — section not applicable.*
