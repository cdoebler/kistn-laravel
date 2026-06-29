<?php

use Mockery;
use Kistn\Exception\InventoryException;
use Kistn\InventoryPusher;

afterEach(fn () => Mockery::close());

test('inventory:push calls pushAll and exits 0', function () {
    $mock = Mockery::mock(InventoryPusher::class);
    $mock->shouldReceive('pushAll')->once();
    $this->app->instance(InventoryPusher::class, $mock);

    $this->artisan('inventory:push')
        ->expectsOutput('Inventory pushed successfully.')
        ->assertExitCode(0);
});

test('inventory:push exits 1 on InventoryException', function () {
    $mock = Mockery::mock(InventoryPusher::class);
    $mock->shouldReceive('pushAll')->andThrow(new InventoryException('api unreachable'));
    $this->app->instance(InventoryPusher::class, $mock);

    $this->artisan('inventory:push')
        ->expectsOutputToContain('Inventory push failed: api unreachable')
        ->assertExitCode(1);
});
