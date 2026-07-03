# Feature Specification: Partial Sync Scope

**Feature Branch**: `002-partial-sync-scope`

**Created**: 2026-07-03

**Status**: Draft

**Input**: User description: "l'estensione per firefox deve permettere di specificare un punto dell'albero dei segnalibri da cui iniziare la sincronizzazione. L'utente potrebbe non voler sincronizzare tutti i suoi segnalibri ma solo una sezione 'pubblica'"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Choose Sync Root Folder (Priority: P1)

A user opens the extension popup, sees a list of their top-level bookmark folders, selects one as the sync root (e.g., "Public"), and confirms. From that point on, only bookmarks and subfolders under the chosen root are synced to the backend. The user can change the selection at any time.

**Why this priority**: This is the core value of the feature — users must be able to select what to sync.

**Independent Test**: Open the extension popup, observe the folder picker, select a folder, trigger sync, and verify that only bookmarks under that folder appear on the backend.

**Acceptance Scenarios**:

1. **Given** the user is authenticated, **When** they open the extension popup, **Then** they see a folder picker showing top-level bookmark folders.
2. **Given** the user selects a folder from the picker, **When** they confirm the selection, **Then** the chosen folder is saved as the sync root.
3. **Given** a sync root is set, **When** sync runs, **Then** only bookmarks and folders under that root are sent to the backend.
4. **Given** the user clears the sync root selection, **When** the next sync runs, **Then** all bookmarks are synced (fallback to full-tree behavior).

---

### User Story 2 - Sync Root Persistence (Priority: P1)

The extension remembers the user's sync root choice across browser restarts. When the user reopens the extension, the previously selected folder is shown as active.

**Why this priority**: Without persistence, the user would have to reconfigure the sync root each time they use the extension.

**Independent Test**: Select a sync root, close Firefox, reopen Firefox, open the extension, and confirm the same folder is still selected.

**Acceptance Scenarios**:

1. **Given** a sync root is configured, **When** the browser is restarted, **Then** the extension remembers the selection.
2. **Given** a sync root is configured, **When** the user opens the popup, **Then** the picker shows the previously selected folder as active.

---

### Edge Cases

- What if the user deletes the selected sync root folder in Firefox? The extension should fall back to full-tree sync and notify the user.
- What if the selected folder is empty? Sync runs and sends zero bookmarks (no error).
- What if the user renames the sync root folder? The folder is identified by its Firefox node ID, so renaming does not break the selection.
- What if the user has hundreds of folders? The folder picker should be scrollable.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The extension MUST allow the user to select any top-level bookmark folder as the sync root.
- **FR-002**: When a sync root is selected, the extension MUST only sync bookmarks and folders under that root.
- **FR-003**: When no sync root is selected, the extension MUST fall back to full-tree sync.
- **FR-004**: The extension MUST persist the sync root selection using `storage.local`.
- **FR-005**: The extension MUST display the currently selected sync root in the popup.
- **FR-006**: The extension MUST allow the user to clear the sync root selection and revert to full-tree sync.
- **FR-007**: If the selected sync root folder no longer exists, the extension MUST fall back to full-tree sync and notify the user.

### Key Entities

- **SyncRoot**: The Firefox bookmark folder ID that the user has chosen as the root of the sync scope. Stored locally in extension storage. When set, the bookmark tree sent to the backend is filtered to only include nodes under this folder.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A user can select a sync root folder in under 30 seconds on first use.
- **SC-002**: After selecting a sync root, subsequent syncs complete correctly with only the selected subtree.
- **SC-003**: The extension correctly falls back to full-tree sync when the sync root is cleared or missing.

## Assumptions

- The sync root is always a top-level Firefox bookmark folder (direct child of the bookmark root or "Other Bookmarks").
- Nested subfolders within the selected root are included automatically.
- The folder is identified by its Firefox node ID, which is stable across sessions.
- No backend changes are required — the filtering happens entirely in the extension before sending the tree payload.
