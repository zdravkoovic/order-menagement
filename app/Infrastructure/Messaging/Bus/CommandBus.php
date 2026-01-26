<?php

namespace App\Infrastructure\Messaging\Bus;

use App\Application\Abstraction\Bus\ICommandBus;
use App\Application\Abstraction\ICommand;
use App\Application\Abstraction\ICommandHandler;
use App\Application\Abstraction\Result;
use App\Application\Errors\ApplicationException;
use App\Application\Errors\Messages\UserErrorMessage;
use App\Application\Errors\Translators\ApplicationExceptionTranslator;
use App\Application\Errors\Translators\DomainExceptionTranslator;
use App\Application\Errors\Translators\InfrastructureExceptionTranslator;
use App\Domain\Shared\Uuid;
use App\Infrastructure\Errors\InfrastructureExceptions;
use DomainException;
use LogicException;


final class CommandBus implements ICommandBus
{
    public function __construct(
        private array $map,
        private ?array $middleware = []
    )
    {}
    
    public function dispatch(ICommand $command): Result
    {
        /** @var ICommandHandler $handler */
        $handler = $this->map[$command::class] ?? throw new LogicException("No handler found for" . $command::class);
        $core = function (ICommand $command) use ($handler) : Uuid | null {
            return $handler->handle($command);
        };
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn ($next, $middleware) => fn (ICommand $command) => $middleware->handle($command, $next),
            $core
        );
        try {
            /** @var ?Uuid $result */
            $result = $pipeline($command);
            return Result::success([$result->value()]);
        } catch (DomainException $domain) {
            $appError = DomainExceptionTranslator::translate($domain);
            return Result::fail($appError);
        } catch (InfrastructureExceptions $infra) {
            $appError = InfrastructureExceptionTranslator::translate($infra);
            return Result::fail($appError);
        } catch (ApplicationException $app) {
            $appError = ApplicationExceptionTranslator::translate($app);
            return Result::fail($appError);
        } catch (\Throwable $e) {
            return Result::fail(new UserErrorMessage($e->getMessage(), 500), 500);
        }
    }
}