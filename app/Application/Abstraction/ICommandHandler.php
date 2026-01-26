<?php

namespace App\Application\Abstraction;

use App\Domain\Shared\Uuid;

interface ICommandHandler
{
    public function handle(ICommand $command) : Uuid | null;
}