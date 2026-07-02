# Kids Party Planner Deployment

## Server Requirements

- PHP 8.2+
- MySQL 8 or MariaDB 10.4+
- Composer 2+
- Apache/Nginx document root pointed to `public/`
- PHP extensions commonly required by Laravel: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `fileinfo`

## First Deployment

1. Upload the project to the server.
2. Point the domain document root to the Laravel `public/` directory.
3. Copy `.env.production.example` to `.env`.
4. Fill real production values in `.env`:
   - `APP_URL`
   - `APP_KEY`
   - MySQL database credentials
   - SMTP mail credentials
   - Razorpay key, secret and webhook secret
   - Google OAuth credentials if Google login is enabled
5. Install production dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

6. Generate the app key if `APP_KEY` is empty:

```bash
php artisan key:generate --force
```

7. Run migrations:

```bash
php artisan migrate --force
```

8. Link public storage:

```bash
php artisan storage:link
```

9. Cache production bootstrap files:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

10. Confirm these folders are writable by the web server:

```text
storage/
bootstrap/cache/
```

## Go-Live Checks

- `APP_ENV=production`
- `APP_DEBUG=false`
- HTTPS/SSL is active
- Admin password is changed from any demo value
- Test mail sends successfully
- Razorpay payment succeeds in the intended mode
- Invoice email is received after payment success
- Admin image upload works and files load from `/storage/...`
- `sitemap.xml` and `robots.txt` load
- Database backup is taken before launch

## Useful Commands

Clear caches after changing `.env`:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run tests only against the testing environment. The included `phpunit.xml` points PHPUnit to its own testing cache files so cached production config is not reused during tests.

Put the site into maintenance mode:

```bash
php artisan down
```

Bring the site back online:

```bash
php artisan up
```
