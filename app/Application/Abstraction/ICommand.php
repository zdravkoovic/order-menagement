<?php

namespace App\Application\Abstraction;

interface ICommand
{
    public function commandId() : string;
}