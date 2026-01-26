<?php

namespace App\Infrastructure\Console\Commands;

use App\Application\Abstraction\Bus\ICommandBus;
use App\Application\Order\Commands\ExpireDraftOrder\ExpireDraftOrderCommand;
use Illuminate\Console\Command;

class ExpireDraftOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:expire-draft-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire draft orders whose expiration time has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(ICommandBus::class)
            ->dispatch(new ExpireDraftOrderCommand());
        return self::SUCCESS;
    }
}
