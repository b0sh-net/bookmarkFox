# Feature Specification: Bookmark Sync

**Feature Branch**: `001-bookmark-sync`

**Created**: 2026-07-03

**Status**: Draft

**Input**: User description: "l'estensione di firefox deve sincronizzare i bookmark con il backend. la sincronizzazione e' monodirezionale, dall'estensione al backend e mai il contrario. deve essere possibile registrarsi sul backend creando un nuovo utente o autenticandosi con un utente esistente. la registrazione e' estremamente semplice e richiede solo email, che funziona anche da nome utente e password. l'estensione di firefox richiede l'inserimento di utente e password per funzionare, li valida con il back end e inizia la sincronia. Il backend espone i bookmark in una serie di pagine pubblicamente visibili, con aspetto moderno e gradevole, e una struttura della url che richiama la struttura in cartelle dei bookmark, qualcosa tipo https://bookmarkFox.it/<nomeUtente>/<nomeCartella>/<nomeSottoCartella> . la persistenza del backend si basa su mysql."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - User Registration and Authentication (Priority: P1)

A user registers on the backend by providing an email address (which serves as their username) and a password. Once registered, the user can log in to obtain authentication credentials that allow the Firefox extension to communicate with the backend.

**Why this priority**: Registration and authentication are prerequisites for every other feature. Without an account, no bookmark data can be stored or associated with a user.

**Independent Test**: Can be fully tested by navigating to the registration page, submitting email + password, receiving confirmation, then logging in with the same credentials and receiving a valid authentication token.

**Acceptance Scenarios**:

1. **Given** a new user is on the registration page, **When** they enter a valid email and password and submit, **Then** the account is created and the user receives confirmation.
2. **Given** a registered user is on the login page, **When** they enter their email and correct password, **Then** they receive a valid authentication token.
3. **Given** a registered user is on the login page, **When** they enter an incorrect password, **Then** authentication is denied with a clear error message.

---

### User Story 2 - Bookmark Sync from Extension (Priority: P1)

A user installs the Firefox extension, enters their email and password to authenticate, and the extension syncs their Firefox bookmarks (folders, URLs, titles) to the backend. Sync is one-directional: from the extension to the backend only. Bookmarks that already exist on the backend are updated; new bookmarks are created.

**Why this priority**: This is the core value proposition of the project. Without sync, the backend has no data to display.

**Independent Test**: Can be tested by installing the extension, authenticating, confirming the sync starts, and verifying that bookmarks appear in the backend database and public pages.

**Acceptance Scenarios**:

1. **Given** a user is authenticated in the extension, **When** the extension syncs bookmarks, **Then** all Firefox bookmark folders and URLs are mirrored on the backend.
2. **Given** a user adds a new bookmark in Firefox, **When** the next sync occurs, **Then** the new bookmark appears on the backend.
3. **Given** a user modifies a bookmark in Firefox, **When** the next sync occurs, **Then** the bookmark is updated on the backend (not duplicated).
4. **Given** a user deletes a bookmark in Firefox, **When** the next sync occurs, **Then** the bookmark is removed from the backend.

---

### User Story 3 - Public Bookmark Browsing Pages (Priority: P2)

The backend publicly exposes each user's bookmarks as browsable HTML pages organized by folder hierarchy. The URL structure mirrors the folder tree: `https://bookmarkFox.it/<username>/<folder>/<subfolder>`. Pages have a modern, pleasant visual appearance.

**Why this priority**: This is the public-facing output of the synced data. It provides value to both the bookmark owner (who can share their collection) and visitors (who can browse public bookmarks).

**Independent Test**: Can be tested by navigating to a user's public page URL, confirming bookmarks are displayed, navigating into subfolders via URL, and verifying the visual presentation is clean and modern.

**Acceptance Scenarios**:

1. **Given** a user has synced bookmarks, **When** a visitor navigates to `https://bookmarkFox.it/<username>`, **Then** the user's top-level folders and bookmarks are displayed.
2. **Given** a folder exists, **When** a visitor navigates to `https://bookmarkFox.it/<username>/<folder>`, **Then** the folder contents (subfolders and bookmarks) are displayed.
3. **Given** nested folders exist, **When** a visitor navigates to `https://bookmarkFox.it/<username>/<folder>/<subfolder>`, **Then** the subfolder contents are displayed.
4. **Given** a username does not exist, **When** a visitor navigates to the URL, **Then** a clear "user not found" page is shown.

---

### Edge Cases

- What happens when a user has thousands of bookmarks? Pages should load efficiently with pagination or lazy loading.
- How does the system handle malformed bookmark URLs (e.g., `chrome://` or `about:` URLs)? Non-HTTP/HTTPS URLs may be filtered or stored with a note.
- What happens if the extension syncs while the user has no internet connection? The extension queues the sync and retries when connectivity is restored.
- What happens if two users have the same folder structure? Each namespace is isolated by username.
- How are special characters in folder names handled in the URL (e.g., spaces, accented characters)? They should be URL-encoded.
- What happens if a user wants to stop sharing their bookmarks? No account-level privacy controls are specified yet.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: Users MUST be able to register with an email (which serves as username) and a password.
- **FR-002**: Users MUST be able to log in with email and password to receive an authentication token.
- **FR-003**: Unauthenticated API requests MUST be rejected with an appropriate error.
- **FR-004**: The Firefox extension MUST allow users to enter email and password and authenticate against the backend.
- **FR-005**: The Firefox extension MUST sync the user's Firefox bookmarks (folders, titles, URLs) to the backend.
- **FR-006**: Sync MUST be one-directional: from the extension to the backend only.
- **FR-007**: Already-synced bookmarks MUST be updated (not duplicated) on subsequent syncs.
- **FR-008**: The backend MUST expose bookmarks as publicly visible HTML pages.
- **FR-009**: Public page URLs MUST follow the structure `/<username>/<folder>/<subfolder>`.
- **FR-010**: Public pages MUST have a modern, responsive, and visually pleasant design.
- **FR-011**: The backend MUST persist all data (users, folders, bookmarks) in MySQL.

### Key Entities

- **User**: Represents a registered person. Key attributes: email (username), password (hashed), unique identifier. Has many folders and bookmarks.
- **Folder**: Represents a bookmark folder belonging to a user. Key attributes: name, parent folder (for nesting), owner. Part of a tree structure per user.
- **Bookmark**: Represents a single bookmark entry. Key attributes: title, URL, parent folder, position/order within the folder. Belongs to exactly one folder.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A new user can complete registration and log in within 30 seconds.
- **SC-002**: A user with 500 bookmarks across 20 folders can sync all bookmarks to the backend within 60 seconds on a standard broadband connection.
- **SC-003**: Public pages load and display fully within 2 seconds for a folder containing up to 100 bookmarks.
- **SC-004**: 95% of users successfully complete their first sync without errors on the first attempt.
- **SC-005**: Public bookmark pages render correctly (no broken layout, readable text) on desktop and mobile browsers.

## Assumptions

- Users have a stable internet connection during sync operations.
- The Firefox extension uses the standard Firefox bookmarks API (Manifest V3).
- Email addresses are assumed to be unique and valid (no email verification step in v1).
- Sync is triggered by the user (e.g., a "Sync Now" button) and also runs periodically while the extension is active.
- No password recovery/reset mechanism is needed for the initial version.
- Bookmark sync includes folder hierarchy, bookmark titles, and URLs. Other bookmark metadata (tags, keywords, date added) may be excluded in the initial version.
- Public pages are readable by anyone without authentication. No password-protected or private bookmark views are specified.
