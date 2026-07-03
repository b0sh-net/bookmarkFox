# Feature Specification: Configure Production API URL

**Feature Branch**: `003-configure-production-url`

**Created**: 2026-07-03

**Status**: Draft

**Input**: User description: "la url base per le api sara https://bookmarkfox.b0sh.net"

## User Scenarios & Testing

### User Story 1 - Extension Syncs with Production Backend (Priority: P1)

As a user of bookmarkFox, the extension must communicate with the configured production API so that bookmarks are synced to the correct backend.

**Why this priority**: Without the correct API URL the extension cannot connect to the production server, making the extension non-functional.

**Independent Test**: Can be tested by verifying the extension's API calls are sent to `https://bookmarkfox.b0sh.net/api/v1/...` endpoints and receive valid responses.

**Acceptance Scenarios**:

1. **Given** the extension is configured with `API_BASE = https://bookmarkfox.b0sh.net/api/v1`, **When** the extension makes any API request, **Then** the request URL starts with `https://bookmarkfox.b0sh.net/api/v1`
2. **Given** the extension points to the production URL, **When** a user performs a bookmark sync, **Then** requests reach the production backend successfully

---

### Edge Cases

- What happens if the production URL changes later? The configuration should be easy to locate and update.
- How does the extension handle migration from localhost to production? This is a single config change — no data migration needed on the extension side.

## Requirements

### Functional Requirements

- **FR-001**: The extension MUST use `https://bookmarkfox.b0sh.net/api/v1` as the base URL for all API requests.
- **FR-002**: The API base URL MUST be defined in a single, easy-to-locate configuration constant in each extension file that makes API calls.
- **FR-003**: All existing API endpoint paths (`/bookmarks/sync`, `/bookmarks`, etc.) MUST be preserved — only the base URL changes.

### Key Entities

- **API_BASE constant**: The string value defining the root URL for all REST API calls made by the extension.

## Success Criteria

### Measurable Outcomes

- **SC-001**: Extension API requests are sent to `https://bookmarkfox.b0sh.net/api/v1/*` instead of `http://localhost:8000/api/v1/*`.
- **SC-002**: All existing sync and bookmark management functionality continues to work after the URL change.

## Assumptions

- The production backend is already deployed and accessible at `https://bookmarkfox.b0sh.net`
- The API contract (endpoints, request/response shapes) is identical between localhost and production
- No authentication credentials need to change as part of this configuration — only the base URL
