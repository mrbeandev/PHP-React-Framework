# AGENTS.md - MRBEANDEV TEMPLATES

## Current State

This workspace contains the **Unified PHP-React Coexistence Framework**, a professional boilerplate designed to merge PHP's backend simplicity with React's frontend robustness.

### Active Project: `PHP-React Unified Coexistence Template`

A "Next.js-like" unified architecture for the PHP ecosystem.

#### Key Architectural Goals
- **Eliminate Fragmentation**: Use a single web root (`public/`) and a unified router (`index.php`) to handle the entire application lifecycle.
- **Modern DX for PHP**: Bring Vite's Hot Module Replacement (HMR) and modern toolchains to traditional PHP environments.
- **Eloquent Everywhere**: Provide a clean path to use Laravel's Eloquent ORM in a lightweight, non-framework-dependent way.
- **Deployment Parity**: Ensure the development environment behaves like production by using a unified routing entry point.
- **Dynamic SEO**: Built-in support for server-side SEO injection, allowing dynamic metadata based on the current URI.

#### Folder Structure

- `/app/Models`: Eloquent models and PHP business logic.
- `/src`: React source code (React 19 + Tailwind 4).
- `/public`: The web root (publicly accessible).
    - `index.php`: The intelligent router (API + SPA + Static Assets).
    - `/dist`: Compiled production assets.
- `bootstrap.php`: Centralized initialization (Eloquent, Dotenv, Logging).
- `migrate.php`: Database schema management script.
- `seed.php`: Data seeding script for demo/testing.

#### Setup & Usage

1. **Installation**:
    ```bash
    composer install && npm install
    ```
2. **Database**:
    ```bash
    touch database/database.sqlite
    npm run migrate
    npm run seed # Adds demo data for the 'TaskFlow' example
    ```
3. **Development**:
    ```bash
    npm run dev
    ```
    _Starts PHP (8000) and Vite (5173). Access via Vite URL for HMR._

4. **Production Build & Preview**:
    ```bash
    npm run build
    npm run preview # Runs production code through the PHP router
    ```

## Coding Standards

- **ORM**: Use Eloquent for all database operations.
- **Routing**: API routes must stay within the `/api` prefix in `public/index.php`.
- **UI**: Prioritize Shadcn UI and premium Tailwind 4 aesthetics.
- **Logging**: Use the unified logging setup; check `php_errors.log` for backend failures.

## Dynamic SEO System

The framework includes a powerful SEO injection system that works even for SPAs.

1. **Configuration**:
    - Toggle via the `settings` table: `enable_dynamic_seo` (1 or 0).
    - Manage meta tags via the `seo` table.
2. **How it works**:
    - `public/index.php` intercepts requests to non-API routes.
    - It matches the current URI against the `seo` table.
    - If a match is found, it injects `<title>`, `<meta>`, and Open Graph tags into the HTML during the initial serve.
3. **Seeding**:
    - Use `php seed.php` to populate default SEO values for `/` and `/about`.
