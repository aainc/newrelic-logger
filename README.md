# newrelic-logger

## Description
Laravel 上で Newrelic Logs を利用するとき、ログに出力される内容をコントロールするためのライブラリ。

message に送信された文字列や配列、例外をパースして Newrelic Logs へ送信する。

## Requirements
* PHP 7.1 or higher
* Laravel 5.4 or higher
* Newrelic api key

## Get Started
### Installation
~~~ 
composer require aainc/newrelic-logger
~~~

### Initialize
config/logging.php

~~~PHP
      'stack' => [
            'driver' => 'stack',
            'channels' => ['single','newrelic'],
            'ignore_exceptions' => false,
      ],
        
      ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        
      'newrelic' => [
            'driver' => 'monolog',
            'handler'=> \NewRelic\Monolog\Enricher\Handler::class,
            'tap' => [\Aainc\NewrelicLogger\NewRelicLogs::class],
            'level' => env('LOG_LEVEL', 'debug'),
            'formatter' => 'default',
        ],
~~~


config/aa-newrelic-logger.php


~~~PHP

<?php

return [
    // Minimum log level describing additional information (default:DEBUG)
    'log_level'   => \Monolog\Logger::DEBUG,
    // This may be useful if the New Relic PHP agent is not installed, or if you wish to log to a different account or region.
    // (default:null)
    'license_key' => null,
    // Can use this to log output with server environment variable information.
    'extra_data'  => []
];

~~~

Example

~~~PHP
<?php

return [
    'log_level'     => \Monolog\Logger::DEBUG,
    'license_key'   => getenv('NEWRELIC_LICENSE_KEY'),
    'extra_data'    => [
        'host_name' => getenv('HOST_NAME')
    ]
];
~~~

### Send Logs

#### Send text message

~~~PHP
Log::debug('Send text);
~~~

#### Send array message

~~~PHP
Log::debug('array', [
    'hoge' => 'fuga',
    'hogehoge' => 'fugafuga'
]);
~~~

#### Send Exception

~~~PHP
Log::debug(new Exception('message');
~~~

