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
        $introspectionProcessor = new IntrospectionProcessor(config('aa-newrelic-logger.log_level', Logger::DEBUG), ['Illuminate\\']);
        $formatter = new Formatter();
        $processor = new Processor();

        foreach ($logger->getHandlers() as $handler) {
            if (is_a($handler, 'NewRelic\Monolog\Enricher\Handler')) {
                $licenseKey = config('aa-newrelic-logger.license_key');
                if ($licenseKey !== null) {
                    $handler->setLicenseKey($licenseKey);
                }
            }
            $handler->setFormatter($formatter);
            $handler->pushProcessor($processor);
            $handler->pushProcessor($introspectionProcessor);
        }
    }
}
