<?php

namespace App\Application\Abstraction;

interface IQueryHandler
{
    public function handle(IQuery $query) : Dto;
}