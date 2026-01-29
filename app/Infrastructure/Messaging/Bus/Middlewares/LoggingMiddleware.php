<?php

namespace App\Infrastructure\Messaging\Bus\Middlewares;

use App\Application\Abstraction\Bus\IMiddleware;
use App\Application\Abstraction\IAction;
use App\Application\Errors\ApplicationException;
use App\Domain\Shared\Uuid;
use App\Infrastructure\Errors\InfrastructureExceptions;
use DomainException;
use Psr\Log\LoggerInterface;

final class LoggingMiddleware implements IMiddleware
{
    public function __construct(
        private LoggerInterface $logger
    ){}

    public function handle(IAction $action, callable $next): ?array
    {
        $ctx = array_merge([
            'command' => class_basename($action),
        ], $action->toLogContext());

        $this->logger->info("Dispatching command \n", $ctx);

        try {
            /** @var ?Uuid $result */
            $result = $next($action);

            if(!$result) $logResult = [
                "status" => true,
                "result" => $result
            ];

            if(!$result) $this->logger->info('Operation ' . class_basename($action) . ' completed', array_merge($ctx, ['result' => $logResult]));

            return $result;

        } catch(DomainException $domain) {
            $this->logger->warning(class_basename($action) . " failed \n", array_merge(
                $ctx, 
                [ 'exception' => ['class' => class_basename($domain), 'message' => $domain->getMessage()]
            ]));
            throw $domain;
        } catch(InfrastructureExceptions $infra) {
            $this->logger->warning(class_basename($action) . " failed \n", array_merge([
                $ctx,
                ['exception' => ['class' => class_basename($infra), 'message' => $infra->getMessage()]]
            ]));
            throw $infra;
        } catch (ApplicationException $app) {
            $this->logger->warning(class_basename($action) . " failed \n", array_merge([
                $ctx,
                ['exception' => ['class' => class_basename($app), 'message' => $app->getMessage()]]
            ]));
            throw $app;
        } 
        catch (\Throwable $e) {
            $this->logger->error(class_basename($action) . " failed unexpectedly \n", array_merge($ctx, [
                'exception' => ['class' => $e::class, 'message' => $e->getMessage(), 'stack' => app()->isLocal() ? $e->getTraceAsString() : null]
            ]));
            throw $e;
        }
    }
}
