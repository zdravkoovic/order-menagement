<?php

namespace App\Infrastructure\Messaging\Bus;

use App\Application\Abstraction\Bus\IQueryBus;
use App\Application\Abstraction\Dto;
use App\Application\Abstraction\IQuery;
use App\Application\Abstraction\IQueryHandler;
use App\Application\Abstraction\Result;
use App\Application\Errors\ApplicationException;
use App\Application\Errors\Messages\UserErrorMessage;
use App\Application\Errors\Translators\DomainExceptionTranslator;
use App\Application\Errors\Translators\InfrastructureExceptionTranslator;
use App\Infrastructure\Errors\InfrastructureExceptions;
use DomainException;
use LogicException;

final class QueryBus implements IQueryBus
{
    public function __construct(
        private array $map,
        private ?array $middleware = []
    ) {}
    public function dispatch(IQuery $query): Result
    {
        /** @var IQueryHandler $handler */
        $handler = $this->map[$query::class] ?? throw new LogicException("Handler method is not provided for the query " . $query::class);
        $core = function (IQuery $query) use ($handler) : Dto {
            return $handler->handle($query);
        };
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn ($next, $middleware) => fn (IQuery $query) => $middleware->handle($query, $next),
            $core
        );
        try {
            $result = $pipeline($query);
            return Result::success($result);
        } catch (DomainException $domain) {
            $appError = DomainExceptionTranslator::translate($domain);
            return Result::fail($appError, $appError->httpStatus);
        } catch (ApplicationException $app) {
            $appError = DomainExceptionTranslator::translate($app);
            return Result::fail($appError, $appError->httpStatus);
        } catch (InfrastructureExceptions $infra){
            $appError = InfrastructureExceptionTranslator::translate($infra);
            return Result::fail($appError, $appError->httpStatus);
        }
        catch (\Throwable $th) {
            // return Result::fail(new UserErrorMessage("Internal server error.", 500), 500);
            throw $th;
        }
    }
}