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

~~~
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
            'with' => 
                'key' => 'Your Newrelic api key'
            ]
        ],
~~~


### Send Logs

#### Send text message

~~~
Log::debug('Send text);
~~~

#### Send array message

~~~
Log::debug('array', [
    'hoge' => 'fuga',
    'hogehoge' => 'fugafuga'
]);
~~~

#### Send Exception

~~~
Log::debug(new Exception('message');
~~~

