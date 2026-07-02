<?php

declare(strict_types=1);

namespace Kistn\Laravel\Console;

use Illuminate\Console\Command;
use Kistn\Exception\InventoryException;
use Kistn\InventoryPusher;

class PushCommand extends Command
{
    protected $signature = 'kistn:push';

    protected $description = 'Push package inventory to the Kistn server';

    public function handle(InventoryPusher $pusher): int
    {
        try {
            $pusher->pushAll();
            $this->info('Inventory pushed successfully.');

            return self::SUCCESS;
        } catch (InventoryException $e) {
            $this->error('Inventory push failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
