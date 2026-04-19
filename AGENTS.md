# Dashboard 3TJ — Agent Guidelines

Point-of-Sale (POS) application built with **Laravel 10** + **Sanctum** + **Spatie Query Builder**. SPA frontend served by Blade shell views; all data comes from a versioned JSON API.

See [README.md](README.md) for setup and feature overview.

## Build & Test

```bash
# Install PHP dependencies
composer install

# Copy and configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and seed (creates admin/admin and danar/danar accounts)
php artisan migrate --seed

# Start dev server
php artisan serve

# Run tests (reads .env — no isolated test DB configured yet)
php artisan test
```

> **Note:** SQLite in-memory override is commented out in `phpunit.xml`. Tests run against the configured `.env` database.

## Architecture

```
routes/console.v1.php   ← ALL API routes (routes/api.php is empty)
app/Http/Controllers/V1/{Domain}/
app/Http/Requests/Console/V1/
app/Http/Resources/Console/V1/
app/Repositories/{Domain}/
app/Models/
```

- **Web routes** (`routes/web.php`) are SPA shells — each returns a single Blade view with no auth middleware.
- **API routes** are prefixed `/api/console/v1` and grouped under `auth:sanctum` (except `POST login`).

## Key Conventions

### Repository Pattern
All data access goes through the repository layer. Controllers **never** query models directly.

- Interface: [app/Repositories/EloquentRepositoryInterface.php](app/Repositories/EloquentRepositoryInterface.php)
- Base implementation: [app/Repositories/BaseRepository.php](app/Repositories/BaseRepository.php) — wraps `spatie/laravel-query-builder`
- Domain repos extend `BaseRepository` and inject a Model via constructor
- Example: [app/Repositories/Sale/SaleRepository.php](app/Repositories/Sale/SaleRepository.php)

### UUIDs as Public IDs
All models expose `uuid` externally. Integer PKs never appear in API responses or route parameters.

- `getRouteKeyName()` returns `'uuid'` on all models
- UUIDs are auto-generated in the model's `boot()` → `creating` event
- Form Requests resolve `uuid` → integer `id` in `passedValidation()` before passing to repositories

### API Responses
- List endpoints: `JsonResource::collection()->additional(['meta' => [...]])`
- Auth endpoint: raw `response()->json()`
- All resources use the [RelationShortcut](app/Traits/RelationShortcut.php) trait for safe relation access (`getPropWhenLoaded`, `whenSumLoaded`, etc.)

### Database Writes
Wrap all operations touching more than one table in `DB::transaction()`.

### Soft Deletes & Restore
Soft-deleted resources are restored via dedicated `GET {resource}/{id}/restore` endpoints (not PATCH/PUT). Repositories use `onlyTrashed()` + `getRouteKeyName()` for recovery.

### Currency Formatting
Use Indonesian locale inline in Resources:
```php
"Rp" . number_format($value, 2, ",", ".")
```

### Authentication
- Field is `username` (not `email`) — renamed via migration
- Roles use string PKs: `'admin'` / `'user'` in the `user_roles` table
- Authorization checks in controllers: `abort_if(Auth::user()->userRole->id === 'user', 403, ...)`

### BaseModel
All domain models extend [app/Models/BaseModel.php](app/Models/BaseModel.php) (which sets `$guarded = []`). `User` extends `Authenticatable` instead.

## Adding a New Resource

1. Migration → Model (extend `BaseModel`, add `uuid` in `boot()`, set `getRouteKeyName()`)
2. Repository class extending `BaseRepository`
3. FormRequest(s) in `app/Http/Requests/Console/V1/`
4. `JsonResource` in `app/Http/Resources/Console/V1/` — use `RelationShortcut` trait
5. Controller in `app/Http/Controllers/V1/{Domain}/` — inject repository in constructor
6. Register routes in `routes/console.v1.php`
