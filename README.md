# kistn/laravel-client

Laravel package wrapping [kistn/php-client](https://github.com/cdoebler/kistn-php). Provides auto-discovery service provider and an Artisan command.

## Installation

```bash
composer require kistn/laravel
```

Auto-discovered via `extra.laravel.providers` — no manual registration needed.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=kistn-config
```

Set credentials in `.env`:

```env
KISTN_BASE_URL=https://your-server.example
KISTN_PROJECT_ID=your-project-uuid-here
KISTN_TOKEN=your-api-token-here
```

`work_dir` defaults to `base_path()` (project root). `cache_path` defaults to `storage_path('app/private/kistn/.inventory.hash')`.

### File transmission

By default the package uploads your lock and manifest files to the Kistn server
along with the inventory (`composer.lock`, `composer.json`, `vendor/composer/installed.json`,
`package-lock.json`, `package.json`). Control this per ecosystem:

```env
KISTN_TRANSMIT_COMPOSER_FILES=true   # true | false | on-demand
KISTN_TRANSMIT_NPM_FILES=true        # true | false | on-demand
```

- `true` — always upload.
- `false` — never upload (inventory metadata only).
- `on-demand` — upload only when the package manager CLI is unavailable server-side.

Any unrecognized value falls back to `false` (never upload). Set these to `false`
if you do not want manifest contents leaving your environment.

## Usage

```bash
php artisan inventory:push
```

Runs all configured collectors (Composer + npm) and pushes changed inventory to the server. Safe to run on every deploy — skips push when nothing changed.

## How It Works

The `ServiceProvider` binds `InventoryPusher` as a singleton, wiring up:
- `InventoryClient` backed by Guzzle
- `ComposerCollector` pointing at `work_dir`, with `installed.json` preferred over `composer.lock` when present
- `NpmCollector` pointing at `work_dir`
- `LocalHashCache` at `cache_path`

See [kistn/php-client](https://github.com/cdoebler/kistn-php) for the full push flow.

## Testing

```bash
composer run pest
composer run ci:check   # phpstan + rector:check + pest
```
