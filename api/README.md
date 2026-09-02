# Api-SDUI-App

Laravel 13 host for Stac SDUI screens. Uses [stac-php](../SDUI-Core) builders and Sanctum bearer tokens.

## Contract

- `POST /auth/register` `{ name, email, password, password_confirmation }` → `{ token, user }`
- `POST /auth/login` `{ email, password }` → `{ token, user }`
- `POST /auth/logout` (auth)
- `GET /sdui/screens/{name}` (auth) → Stac JSON (`home`, `details`, `form`)
- `POST /sdui/actions/{name}` (auth) — `profile` updates name/email

The `form` screen `validatorRules` come from [`UpdateProfileRequest`](app/Http/Requests/UpdateProfileRequest.php) via [`LaravelRulesToStacMapper`](app/Sdui/Validation/LaravelRulesToStacMapper.php). Failed submits return Laravel 422 `{ message, errors }`. The Symfony Form bridge lives in [`sdui/symfony`](../SDUI-Symfony).

Static screens are cached as `sdui:screen:{name}:{locale}`.

## Local (no Docker)

SQLite is the default. Redis is optional; tests use the array cache.

```bash
composer install
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

## Docker

php-fpm, nginx (port 8000), Redis, MySQL 8.4.

```bash
composer install
docker compose up -d --build
docker compose exec php php artisan migrate
```

Point the Flutter host at `http://127.0.0.1:8000` (Android emulator: `http://10.0.2.2:8000`).
