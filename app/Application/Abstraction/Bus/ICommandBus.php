<?php

namespace App\Application\Abstraction\Bus;

use App\Application\Abstraction\ICommand;
use App\Application\Abstraction\Result;

interface ICommandBus
{
    public function dispatch(ICommand $command) : Result;
}