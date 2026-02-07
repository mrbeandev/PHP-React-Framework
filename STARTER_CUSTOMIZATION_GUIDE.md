# Starter Customization Guide

This template is designed for teams that want **PHP as backend** and **React as frontend** without using a full PHP framework.

Use this guide when starting a new project so you can safely remove demo parts and keep the core architecture intact.

## Keep these files (core architecture)

Do not delete these unless you are intentionally replacing the backend architecture.

- `public/index.php` - front controller entry point.
- `bootstrap.php` - environment, logging, and Eloquent bootstrap.
- `app/Core/*` - request/response, router, container, validation, exceptions.
- `config/*.php` - centralized environment-backed application config.
- `routes/api.php` - API route definitions.
- `routes/web.php` - SPA + static asset fallback.
- `app/Providers/AppServiceProvider.php` - container bindings.
- `app/Http/Middleware/CorsMiddleware.php` - API CORS support.
- `composer.json` + `vendor/` (after install) - backend dependencies/autoload.
- `vite.config.ts` - frontend build + dev proxy behavior.

## Safe to replace/remove (demo-specific)

These are demo app examples and can be replaced with your own domain.

- `src/App.tsx` and related UI for TaskFlow demo.
- `app/Models/Todo.php`.
- `app/Controllers/Api/TodoController.php`.
- Todo routes in `routes/api.php`.
- `seed.php` demo task data.

## Optional features (remove only if you do not need them)

### Dynamic SEO module

If you do not need database-driven SEO injection, you can remove:

- `app/Models/Seo.php`
- `app/Models/Setting.php`
- `app/Controllers/Api/SeoController.php`
- `app/Controllers/Api/SettingController.php`
- SEO/settings routes in `routes/api.php`
- SEO lookup logic inside `app/Controllers/Web/FrontendController.php`
- `seo` and `settings` table creation in `migrate.php`
- SEO/setting seed data in `seed.php`

If removed, keep a static `<title>` and meta tags in your frontend `index.html` build template.

### API key guard

If you do not need API key protection for admin-like routes, remove:

- `app/Http/Middleware/ApiKeyAuthMiddleware.php`
- Its usage in `routes/api.php`
- `API_KEY` from `.env.example` (optional)

### Request logs middleware

If you do not want per-request logs in `php_errors.log`, remove:

- `app/Http/Middleware/RequestLoggingMiddleware.php`
- Its usage in `routes/api.php`

## First steps for a new project

1. Copy env file: `cp .env.example .env`
2. Set app/database values in `.env`.
3. Replace demo API controllers with your own modules.
4. Update `routes/api.php` to match your domain endpoints.
5. Replace demo React UI in `src/`.
6. Update `migrate.php` and `seed.php` with your schema/data.
7. Keep API base in frontend as `/api/v1`.

## Route conventions

- Keep backend routes versioned under `/api/v1`.
- Keep web fallback in `routes/web.php` for React SPA routes.
- Add middleware at route group level when possible.

## Common mistakes to avoid

- Deleting `public/index.php` and expecting SPA routing to still work.
- Moving API paths away from `/api/v1` without updating frontend fetch base.
- Removing `bootstrap.php` or Dotenv loading while still using env variables.
- Deleting `routes/web.php` and breaking deep-link refresh for React routes.

## Minimal backend checklist

If you want a very small backend, the minimal structure is:

- `public/index.php`
- `bootstrap.php`
- `app/Core/*`
- `routes/api.php`
- `routes/web.php`
- one model + one controller + one route group

Everything else can evolve from there.
