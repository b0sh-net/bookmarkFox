# Auth API Contract

**Base URL**: `https://bookmarkfox.it/api/v1`

**Auth Scheme**: Bearer token (Laravel Sanctum)

---

## POST /register

Create a new user account and return an API token.

### Request

```json
{
  "email": "user@example.com",
  "password": "secret123"
}
```

### Response 201

```json
{
  "token": "1|abc123def456..."
}
```

### Response 422 (validation error)

```json
{
  "message": "The email field is required.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

---

## POST /login

Authenticate with existing credentials and return an API token.

### Request

```json
{
  "email": "user@example.com",
  "password": "secret123"
}
```

### Response 200

```json
{
  "token": "1|abc123def456..."
}
```

### Response 401

```json
{
  "message": "Invalid credentials."
}
```

---

## POST /logout

Revoke the current API token. Requires Bearer token.

### Request

Headers: `Authorization: Bearer 1|abc123def456...`
Body: none

### Response 200

```json
{
  "message": "Logged out."
}
```

---

## GET /me

Return the authenticated user's profile.

### Request

Headers: `Authorization: Bearer 1|abc123def456...`

### Response 200

```json
{
  "id": 1,
  "email": "user@example.com"
}
```
