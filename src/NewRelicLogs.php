<?php

declare(strict_types=1);

namespace Aainc\NewrelicLogger;

use Monolog\Logger;
use NewRelic\Monolog\Enricher\Formatter;
use NewRelic\Monolog\Enricher\Processor;

class NewRelicLogs
{
    public function __invoke($logger): void
    {
        $introspectionProcessor = new IntrospectionProcessor(config('aanewreliclogger.log_level', Logger::DEBUG), ['Illuminate\\']);
        $formatter = new Formatter();
        $processor = new Processor();

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
            $handler->pushProcessor($processor);
            $handler->pushProcessor($introspectionProcessor);
        }
    }
}
