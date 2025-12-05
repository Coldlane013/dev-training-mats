### Quick orientation for AI coding agents

- Project type: Laravel backend (PHP ^8.2) with an Inertia + Vue 3 frontend built with Vite. See `composer.json` and `package.json` for exact versions and scripts.
- Auth: Laravel Fortify config lives in `app/Providers/FortifyServiceProvider.php` and maps Fortify views to Inertia pages under `resources/js/pages/auth`.
- Frontend layout: Inertia pages live in `resources/js/pages`. Reusable UI and layouts are under `resources/js/components` and `resources/js/layouts`.
- Service layer: PHP services under `app/Service` (e.g. `CreditService.php`) are lightweight single-responsibility classes. Frontend service wrappers live under `resources/js/services/*` (e.g. `bankService.ts`, `authService.ts`). Prefer using these service files as canonical API clients.

### Architecture & data flow (how the pieces interact)
- Requests: Routes in `routes/*.php` (notably `routes/web.php` and `routes/settings.php`) send requests to controllers under `app/Http/Controllers/*` which return Inertia responses. Example: `routes/web.php` renders `Dashboard` via Inertia.
- Controllers -> Services: Business logic is often delegated to classes in `app/Service/*`; consult these when adding domain logic rather than adding heavy logic inside controllers.
- Frontend -> Backend: Frontend calls through `resources/js/lib/axios-client.ts` and service wrappers in `resources/js/services/*`. Follow existing patterns for authentication (sanctum) and CSRF.
- State: Pinia stores live under `resources/js/stores` and hold client-side state; prefer small, focused stores used by pages/components.

### Developer workflows & exact commands
- Local dev (recommended): use Sail or local PHP/Node installs. The `README.md` documents both; prefer Sail for parity with CI:
  - Setup once: `composer run setup` (runs composer install, copies `.env.example`, runs migrations, `npm install` and `npm run build`).
  - Start dev (composer script with concurrently): `composer run dev` (starts artisan serve, queue listener, pail logger, and vite).
  - Alternative with Sail: `./vendor/bin/sail up -d` then inside Sail run `./vendor/bin/sail npm run dev` and `./vendor/bin/sail artisan migrate`.
- Build & assets: `npm run build` (Vite production build). Use `npm run dev` to run Vite dev server locally (the composer `dev` orchestrates both PHP and Vite).
- Tests: `composer run test` (this clears config cache then runs `php artisan test`). CI also uses Pest (`tests/`), configured in `composer.json` and `tests/Pest.php`.

### Project-specific conventions & patterns
- Inertia pages follow a single-file Vue pattern and are registered under `resources/js/pages`. Use `Inertia::render('...')` in PHP to render these.
- Fortify views are explicitly mapped in `app/Providers/FortifyServiceProvider.php` — do not rely on default blade views for auth flows here.
- Centralized API clients: use `resources/js/services/*` for HTTP calls. Avoid copy-pasting axios config; import `resources/js/lib/axios-client.ts`.
- Layouts & components: follow the existing layout system (`resources/js/layouts/*`) and re-use `AppLayout`, `AuthLayout`, `DashboardLayout` to keep consistency.
- Tests use Pest + Laravel testing helpers. Tests are namespaced under `tests/Feature` and `tests/Unit` and `tests/Pest.php` applies shared traits (e.g., `RefreshDatabase`).

### Integration points & external deps to be aware of
- Auth: `laravel/fortify` and `laravel/sanctum` — changes to auth flows must update Fortify mappings and consider sanctum CSRF cookie flows used by the frontend.
- Frontend toolchain: `vite`, `@vitejs/plugin-vue`, `tailwindcss`, and `prettier`/`eslint` for linting/formatting. See `package.json` scripts (`dev`, `build`, `lint`, `format`).
- Docker/Sail: `laravel/sail` is included for consistent containerized development in CI and developer machines.

### Files to consult when making changes (shortlist)
- Backend entry points & DI: `app/Providers/*` (`FortifyServiceProvider.php`, `AppServiceProvider.php`).
- Domain & services: `app/Service/` (e.g. `CreditService.php`).
- Controllers & requests: `app/Http/Controllers/`, `app/Http/Requests/`.
- Routes: `routes/web.php`, `routes/settings.php`, `routes/api.php` (API patterns).
- Frontend pages: `resources/js/pages/` (auth pages under `pages/auth/`).
- Frontend API clients: `resources/js/lib/axios-client.ts`, `resources/js/services/`.
- Tests: `tests/`, plus `tests/Pest.php` for shared setup.

### How to modify safely (rules for automated edits)
- Use existing service wrappers: if adding an API endpoint, add or update a corresponding file in `resources/js/services/` and reuse `axios-client.ts`.
- Keep Fortify mappings intact: do not remove or rename views referenced in `FortifyServiceProvider.php` without updating the provider.
- Migrations & factories: when changing the DB schema, add a migration under `database/migrations/` and update `database/factories/` for test data.
- Tests: add Pest-style tests in `tests/Feature` and update `tests/Pest.php` only if you understand global test bootstrapping.

If anything in this file is unclear or you want more detail (e.g., example PR diffs that follow the conventions above), tell me which area to expand and I'll iterate.
