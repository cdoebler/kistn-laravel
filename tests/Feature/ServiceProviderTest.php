<?php

use Illuminate\Support\Facades\Artisan;
use Kistn\InventoryPusher;
use Kistn\TransmitMode;

test('InventoryPusher can be resolved from the container', function () {
    $pusher = app(InventoryPusher::class);
    expect($pusher)->toBeInstanceOf(InventoryPusher::class);
});

test('kistn config is available', function () {
    expect(config('kistn.base_url'))->toBe('https://example.com');
    expect(config('kistn.project_id'))->toBe('test-uuid');
    expect(config('kistn.token'))->toBe('test-token');
});

test('inventory:push command is registered', function () {
    expect(Artisan::all())->toHaveKey('inventory:push');
});

test('transmit_composer_files and transmit_npm_files config are available', function () {
    expect(config('kistn.transmit_composer_files'))->toBe('true');
    expect(config('kistn.transmit_npm_files'))->toBe('true');
});

test('transmit config maps to TransmitMode and falls back to Never on invalid value', function () {
    config()->set('kistn.transmit_composer_files', 'false');
    config()->set('kistn.transmit_npm_files', 'bogus');
    app()->forgetInstance(InventoryPusher::class);

    $pusher = app(InventoryPusher::class);

    $reflection = new ReflectionClass($pusher);
    $composer = $reflection->getProperty('transmitComposerFiles')->getValue($pusher);
    $npm = $reflection->getProperty('transmitNpmFiles')->getValue($pusher);

    expect($composer)->toBe(TransmitMode::Never)
        ->and($npm)->toBe(TransmitMode::Never);
});
