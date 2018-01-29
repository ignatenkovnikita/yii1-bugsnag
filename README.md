yii1-bugsnag
===================

Yii1 bugsnag error handler

Installation
------------
Run
```code
composer require "demi/yii1-bugsnag" "~1.0"
```

Configuration
-------------
/protected/config/main.php:
```php
<?php

return [
    'components' => [
        'bugsnag' => [
            'class' => '\demi\bugsnag\yii1\BugsnagComponent',
            'bugsnagApiKey' => '<YOU API KEY>',
            'notifyReleaseStages' => ['production', 'development'],
            'projectRoot' => realpath(__DIR__ . '/../..'),
        ],
        'log' => [
            'class' => 'CLogRouter',
            'routes' => [
                [
                    'class' => '\demi\bugsnag\yii1\BugsnagLogRoute',
                    'levels' => 'error, warning',
                ],
            ],
        ],
    ],
];
```
/protected/config/console.php:
```php
<?php

$mainConfig = require(dirname(__FILE__) . '/main.php');
return [
    'components' => [
        'errorHandler' => [
            'class' => '\demi\bugsnag\yii1\BugsnagErrorHandler',
        ],
        'bugsnag' => $mainConfig['components']['bugsnag'],
        'log' => [
            'class' => 'CLogRouter',
            'routes' => [
                [
                    'class' => '\demi\bugsnag\yii1\BugsnagLogRoute',
                    'levels' => 'error, warning',
                ],
            ],
        ]
    ],
];
```

Examples
--------
```php
<?php

// log exception
Yii::app()->bugsnag->notifyException(\Exception $e);
// log message
Yii::app()->bugsnag->notifyError('TestErrorName', 'Example warning message!', ['foo' => 'bar'], 'warning');

// or native exception
throw \Exception('Example exception message');
// or native log message
Yii::log('Example warning message!', 'warning', 'application.error');
```
