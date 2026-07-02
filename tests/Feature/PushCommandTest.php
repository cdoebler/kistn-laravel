<?php

use Mockery;
use Kistn\Exception\InventoryException;
use Kistn\InventoryPusher;

afterEach(fn () => Mockery::close());

test('kistn:push calls pushAll and exits 0', function () {
    $mock = Mockery::mock(InventoryPusher::class);
    $mock->shouldReceive('pushAll')->once();
    $this->app->instance(InventoryPusher::class, $mock);

    $this->artisan('kistn:push')
        ->expectsOutput('Inventory pushed successfully.')
        ->assertExitCode(0);
});

test('kistn:push exits 1 on InventoryException', function () {
    $mock = Mockery::mock(InventoryPusher::class);
    $mock->shouldReceive('pushAll')->andThrow(new InventoryException('api unreachable'));
    $this->app->instance(InventoryPusher::class, $mock);

    $this->artisan('kistn:push')
        ->expectsOutputToContain('Inventory push failed: api unreachable')
        ->assertExitCode(1);
});
