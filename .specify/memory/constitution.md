<!--
  Sync Impact Report:
  - Version change: 1.0.0 → 1.1.0
  - Modified principles: none renamed
  - Added sections: Principle VI (Module-Level Documentation)
  - Removed sections: none
  - Templates requiring updates:
    - plan-template.md (✅ no changes needed)
    - spec-template.md (✅ no changes needed)
    - tasks-template.md (✅ no changes needed)
  - Follow-up TODOs: create README.md in extension/ and backend/
-->

# bookmarkFox Constitution

## Core Principles

### I. Firefox Extension

The project MUST include a Firefox WebExtension that serves as the client-side
interface for bookmark management. The extension MUST handle user interaction,
browser bookmark API integration, and presentation of bookmark data received
from the backend. It MUST be packaged as a valid Mozilla-compatible add-on.

### II. PHP Backend API

A PHP web application MUST provide the server-side REST API for bookmark
storage, retrieval, and management. The PHP backend MUST expose a clean,
versioned RESTful API and handle all persistent data storage. It MUST NOT
assume a specific frontend beyond the documented API contract.

### III. Authenticated REST API Communication

All communication between the Firefox extension and the PHP backend MUST occur
over authenticated REST APIs. Every API request MUST include valid
authentication credentials (e.g., API token or OAuth). The API contract MUST
be documented and versioned. Unauthenticated requests MUST be rejected with
HTTP 401.

### IV. Modular Independence

The Firefox extension and PHP backend MUST be developed as independent modules
with their own directory structures, dependencies, and build processes. Neither
module MAY depend on the other at build time. The ONLY runtime coupling is the
documented REST API contract.

### V. API Contract First

Before implementing any cross-module feature, the REST API contract (endpoints,
request/response shapes, auth scheme) MUST be agreed upon and documented.
Changes to the API contract MUST be reflected in the documentation before
implementation begins.

### VI. Module-Level Documentation

Every module (extension, backend) MUST contain a `README.md` file at its root
that describes the module's purpose and explains how to deploy, build, or run
it. The README MUST include prerequisites, setup steps, available commands,
and any configuration required. Documentation MUST be kept in sync with the
code — a change that affects the build or deploy process MUST be reflected
in the README.

## Technology Stack

- **Firefox Extension**: JavaScript/HTML/CSS (Manifest V3), WebExtension APIs
- **Backend**: PHP 8.x with a modern framework (e.g., Laravel, Symfony, or Slim)
- **Database**: SQLite or MySQL for bookmark persistence
- **API Format**: JSON over HTTPS
- **Authentication**: Token-based (Bearer tokens)

## Development Workflow

- Each module (extension, backend) MUST have its own directory at repository
  root: `extension/` and `backend/`.
- Each module MUST include a `README.md` with setup, build, and deploy instructions.
- Shared API contracts MUST live in a `contracts/` directory at repository root.
- Integration tests MUST cover the full flow: extension → API → backend → database.
- API changes MUST be backward-compatible within a major version or explicitly
  versioned via URL path (e.g., `/api/v1/`).

## Governance

This constitution supersedes ad-hoc development practices. Amendments require
a documented proposal, team approval, and an update to this file.
All implementation work MUST reference and comply with these principles.

**Version**: 1.1.0 | **Ratified**: 2026-07-03 | **Last Amended**: 2026-07-03
