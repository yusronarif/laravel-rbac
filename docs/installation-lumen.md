---
title: Installation in Lumen
weight: 4
---

NOTE: Lumen is not officially supported by this package. However, the following are some steps which may help get you started.

First, install the package via Composer:

``` bash
composer require yusronarif/laravel-rbac
```

Copy the required files:

```bash
mkdir -p config
cp vendor/yusronarif/laravel-rbac/config/permission.php config/permission.php
cp vendor/yusronarif/laravel-rbac/database/migrations/create_permission_tables.php.stub database/migrations/2018_01_01_000000_create_permission_tables.php
```

You will also need to create another configuration file at `config/auth.php`. Get it on the Laravel repository or just run the following command:

```bash
curl -Ls https://raw.githubusercontent.com/laravel/lumen-framework/5.7/config/auth.php -o config/auth.php
```

Then, in `bootstrap/app.php`, register the middlewares:

```php
$app->routeMiddleware([
    'auth'       => App\Http\Middleware\Authenticate::class,
    'permission' => Yusronarif\RBAC\Middlewares\PermissionMiddleware::class,
    'role'       => Yusronarif\RBAC\Middlewares\RoleMiddleware::class,
]);
```

Also register the config file, service provider, and cache alias:

```php
$app->configure('permission');
$app->alias('cache', \Illuminate\Cache\CacheManager::class);  // if you don't have this already
$app->register(Yusronarif\RBAC\PermissionServiceProvider::class);
```

Now, run your migrations:

```bash
php artisan migrate
```
