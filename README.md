# Unified PHP-React Coexistence Template

A unified PHP + React boilerplate with a framework-style backend core: front controller, router, controllers, middleware pipeline, DI container, and Eloquent models.

For project cleanup and replacement guidance, read `STARTER_CUSTOMIZATION_GUIDE.md`.

## What this template gives you

- Single web root with one front controller at `public/index.php`.
- Versioned API routing under `/api/v1`.
- Controller-based backend structure (no monolithic route logic in one file).
- Middleware support (CORS, request logging, optional API-key auth).
- Dynamic SEO injection for SPA initial HTML responses.

## Quick start

### 1) Prerequisites

- PHP 8.1+
- Composer
- Node.js + npm

### 2) Install dependencies

```bash
composer install && npm install
```

### 3) Configure environment

Create your local env file from the template:

```bash
cp .env.example .env
```

### 4) Initialize database

```bash
touch database/database.sqlite
npm run migrate
npm run seed
```

### 5) Run development

```bash
npm run dev
```

- Frontend (Vite): `http://localhost:5173`
- Backend (PHP): `http://localhost:8000`
- Frontend API base is `/api/v1`.

## API routes

All API endpoints are versioned and mounted in `routes/api.php`:

- `GET /api/v1/todos`
- `POST /api/v1/todos`
- `GET /api/v1/todos/{id}`
- `PUT /api/v1/todos/{id}`
- `DELETE /api/v1/todos/{id}`
- `GET /api/v1/seo`
- `POST /api/v1/seo`
- `GET /api/v1/settings/seo-toggle`
- `POST /api/v1/settings/seo-toggle`

## API key behavior

`Seo` and `settings` endpoints are guarded by `ApiKeyAuthMiddleware`.

- If `API_KEY` is not set in environment, the guard is bypassed (demo mode).
- If `API_KEY` is set, send header `x-api-key: <your-key>`.

## Environment variables

- `APP_ENV` - environment name (`development`, `production`, etc.).
- `APP_DEBUG` - when `true`, PHP displays errors in responses.
- `DB_CONNECTION` - database driver (`sqlite` by default).
- `DB_DATABASE` - SQLite file path, or database name for non-sqlite drivers.
- `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_CHARSET`, `DB_COLLATION`, `DB_PREFIX` - non-sqlite DB settings.
- `API_KEY` - enables API-key auth for protected API routes when set.
- `CORS_ALLOW_ORIGIN`, `CORS_ALLOW_METHODS`, `CORS_ALLOW_HEADERS` - CORS policy for API responses.

Env access is centralized through config files:

- `config/app.php`
- `config/database.php`
- `config/cors.php`
- `config/auth.php`

Runtime code reads values through `App\Core\Support\Config` instead of using `getenv()` directly.

## Middleware order

API middleware is attached at group level in `routes/api.php` and runs in this order:

1. `CorsMiddleware`
2. `RequestLoggingMiddleware`
3. `ApiKeyAuthMiddleware` (only for SEO/settings subgroup)

## Project structure

```text
app/
  Controllers/
    Api/
    Web/
  Core/
    Container/
    Exceptions/
    Http/
    Routing/
    Validation/
  Http/
    Middleware/
  Models/
  Providers/
routes/
  api.php
  web.php
public/
  index.php
  dist/
```

## Useful commands

- `npm run dev` - Start PHP + Vite dev loop.
- `npm run build` - Build production frontend assets.
- `npm run preview` - Serve production build through PHP router.
- `npm run migrate` - Create tables.
- `npm run seed` - Seed demo data.

Built with ❤️ by **MrbeanDev**.

## License

This project is licensed under the MIT License. See `LICENSE`.
