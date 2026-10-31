# Library Management System

A multilingual (English, French, Arabic) library catalogue built with Laravel 12, Blade, Livewire and Sanctum.

- Public catalogue with search, plus a Livewire page that filters by category, language and type
- Admins create, edit and delete books (cover upload, categories, language, type)
- Every book change is recorded in `activity_logs`
- Signed-in users can e-mail themselves a book's details
- Token-authenticated REST API (`/api/books`)

## Requirements

- PHP 8.4 or 8.5 with `pdo_sqlite` or `pdo_mysql` / `pdo_pgsql`
- Composer 2
- Node.js 22+ and npm

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed      # in local, also creates demo books and admin@example.com / password
php artisan storage:link        # serves uploaded covers from storage/app/public
npm ci && npm run build
composer dev                    # web server, queue worker, logs and Vite together
```

## Roles

Registration creates a regular user. Only admins can manage books.

```bash
php artisan users:create --admin          # create an admin interactively
php artisan users:promote someone@example.com           # make an existing user admin
php artisan users:promote someone@example.com --demote  # remove the admin role
php artisan users:list
```

## API

Authenticate to get a token, then send it as `Authorization: Bearer <token>`.

| Method | Endpoint | Who |
|--------|----------|-----|
| POST | `/api/login` (`email`, `password`, optional `device_name`) | anyone (5 requests/min) |
| POST | `/api/logout` | token holder |
| GET | `/api/user` | token holder |
| GET | `/api/books?per_page=15` | token holder (paginated, max 100 per page) |
| GET | `/api/books/{id}` | token holder |
| POST / PUT / DELETE | `/api/books[/{id}]` | admin |

Tokens expire after `SANCTUM_EXPIRATION` minutes (default 7 days). Expired tokens are pruned daily by the scheduler.

## Quality checks

```bash
composer test        # Pest test suite
composer lint        # Pint (PHP style)
npm run lint         # ESLint
npm run format:check # Prettier
```

GitHub Actions runs all of these on pushes and pull requests to `main`.

## Production deployment

1. Set the environment:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`, and a generated `APP_KEY`
   - a real database (MySQL/PostgreSQL recommended over SQLite for concurrent writes)
   - `MAIL_*` for a real mailer
   - `TRUSTED_PROXIES` when running behind a load balancer or reverse proxy
   - `LOG_LEVEL=warning` or `error`
2. Install and build:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan migrate --force
   php artisan db:seed --force   # reference data only (categories, languages, types)
   php artisan storage:link
   php artisan optimize          # caches config, routes, views and events
   ```
3. Create the first admin: `php artisan users:create --admin`.
4. Run the background processes:
   - a queue worker, `php artisan queue:work --tries=3` (the book e-mail is queued), kept running by Supervisor or systemd
   - the scheduler, `* * * * * php artisan schedule:run` in cron
5. Point the web server's document root at `public/`. The health check endpoint is `/up`.

In production, the app forces HTTPS URLs, sends HSTS on secure requests, requires stronger passwords (10+ characters, mixed case and numbers), and blocks destructive database commands such as `migrate:fresh`.

See [PRODUCTION_AUDIT.md](PRODUCTION_AUDIT.md) for the list of issues found and fixed.
