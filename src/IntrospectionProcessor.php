<?php

declare(strict_types=1);

namespace Aainc\NewrelicLogger;

use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;
use ReflectionParameter;

class IntrospectionProcessor implements ProcessorInterface
{
    private $level;

    private array $skipClassesPartials;

    private int $skipStackFramesCount;

    private array $skipFunctions = [
        'call_user_func',
        'call_user_func_array',
    ];

    /**
     * @param int|string $level                The minimum logging level at which this Processor will be triggered
     * @param array      $skipClassesPartials
     * @param int        $skipStackFramesCount
     */
    public function __construct($level = Logger::DEBUG, array $skipClassesPartials = [], int $skipStackFramesCount = 0)
    {
        $this->level = Logger::toMonologLevel($level);
        $this->skipClassesPartials = array_merge(['Monolog\\'], $skipClassesPartials);
        $this->skipStackFramesCount = $skipStackFramesCount;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record): array
    {
        if ($record['level'] < $this->level) {
            return $record;
        }

        $trace = debug_backtrace();

        array_shift($trace);
        array_shift($trace);

        $lineCount = 0;

        while ($this->isTraceClassOrSkippedFunction($trace, $lineCount)) {
            if (isset($trace[$lineCount]['class'])) {
                foreach ($this->skipClassesPartials as $part) {
                    if (strpos($trace[$lineCount]['class'], $part) !== false) {
                        $lineCount++;
                        continue 2;
                    }
                }
            } elseif (in_array($trace[$lineCount]['function'], $this->skipFunctions)) {
                $lineCount++;
                continue;
            }
            break;
        }

        $lineCount += $this->skipStackFramesCount;
        $args = $trace[$lineCount]['args'] ?? null;
        $arguments = [];

        foreach ($args as $key => $value) {
            try {
                $parameter = new ReflectionParameter([$trace[$lineCount]['class'], $trace[$lineCount]['function']], $key);
            } catch (\ReflectionException $e) {
                continue;
            }
            $arguments[$parameter->getName()] = $value instanceof Request ? [
                'params' => $value->all(),
            ] : $value;
        }
        $record['extra'] = array_merge(
            $record['extra'],
            array_merge(
                [
                    'file'     => $trace[$lineCount - 1]['file'] ?? null,
                    'line'     => $trace[$lineCount - 1]['line'] ?? null,
                    'class'    => $trace[$lineCount]['class'] ?? null,
                    'function' => $trace[$lineCount]['function'] ?? null,
                ],
                $arguments
            )
        );

        return $record;
    }

    private function isTraceClassOrSkippedFunction(array $trace, int $index): bool
    {
        if (!isset($trace[$index])) {
            return false;
        }
        return isset($trace[$index]['class']) || in_array($trace[$index]['function'], $this->skipFunctions);
    }
}
