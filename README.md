<!--
	Project: IT12_Brgy_HealthCareMS
	Root README tailored to this repository. Keep concise and actionable.
-->

# IT12_Brgy_HealthCareMS

A Barangay Healthcare Management System built with Laravel. This project provides basic modules for managing users, patients, reports, logs, and administrative tasks for a local barangay health center.

**Repository:** `IT12_Brgy_HealthCareMS`

**Main features (visible in repo):**
- Users management (`app/Models/User.php`, `resources/views/users`)
- Patients management (`resources/views/patients`)
- Reports and logs (`resources/views/reports`, `resources/views/logs`)
- Laravel-based backend with Blade views and Vite-powered frontend assets

**Maintainers:** project team (contact repository owner on GitHub)

## Prerequisites

- PHP 8.1+ (verify with `php -v`)
- Composer (dependency manager)
- Node.js + npm or yarn (for frontend build) — Node 16+ recommended
- A database (MySQL/MariaDB/Postgres/SQLite)

## Getting Started (Development)

1. Clone the repository (if you haven't already):

```powershell
git clone <repository-url> IT12_Brgy_HealthCareMS
cd IT12_Brgy_HealthCareMS
```

2. Install PHP dependencies:

```powershell
composer install
```

3. Install frontend dependencies and build assets:

```powershell
npm install
npm run dev
```

4. Copy `.env` and set environment variables (database, mail, etc.):

```powershell
copy .env.example .env
```

Then edit `.env` and set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.

5. Generate the application key and run migrations:

```powershell
php artisan key:generate
php artisan migrate
php artisan db:seed
```

If you prefer to use SQLite for quick local testing, set `DB_CONNECTION=sqlite` and point `DB_DATABASE` to a file.

6. Serve the application locally:

```powershell
php artisan serve
```

Open `http://127.0.0.1:8000` in your browser.

## Running Tests

This project includes basic tests (see `tests/`). Run tests with:

```powershell
php artisan test
# or if using phpunit directly
vendor\bin\phpunit
```

## Project Structure (high level)

- `app/` — Laravel application code (models, controllers, middleware, providers)
- `routes/` — route definitions (`web.php`, `console.php`)
- `resources/views/` — Blade templates (auth, dashboard, users, patients, reports, logs)
- `resources/js`, `resources/css` — frontend sources (built with Vite)
- `public/` — compiled assets and entry point
- `database/migrations`, `database/seeders` — database setup

## Common Tasks

- Run migrations: `php artisan migrate`
- Rollback last migration: `php artisan migrate:rollback`
- Clear caches: `php artisan optimize:clear`
- Create a controller: `php artisan make:controller NameController`

## Contributing

Contributions are welcome. Typical workflow:

1. Fork the repo
2. Create a feature branch: `git checkout -b feat/your-feature`
3. Make changes, add tests if applicable
4. Commit and push: `git push origin feat/your-feature`
5. Open a pull request describing your changes

Please follow existing code style and conventions (Blade templates, controller patterns).

## Deployment Notes

- Ensure environment variables are set on the server.
- Run `composer install --no-dev` and `npm run build` during deployment.
- Use a process manager (Supervisor) for queue workers if used.

## Where to Look Next

- Views: `resources/views/` (look into `users`, `patients`, `reports` folders)
- Routes: `routes/web.php`
- Controllers: `app/Http/Controllers`

## License

This project follows the repository license (check `composer.json`). If unspecified, assume MIT-compatible usage but confirm with project owners before redistribution.

---

If you'd like, I can also:
- run the migration and dev server for you locally
- commit this README change and push it to `main`
- open a PR instead of pushing directly

Tell me which option you prefer and I will proceed.
