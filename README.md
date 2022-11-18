# Monolog Splunk Formatter

## Installation

Installation is possible using Composer

```
composer require marco-introini/monolog-splunk-formatter
```

## Usage

- create an instance of \Mintdev\Monolog\Formatter\SplunkLineFormatter 
- set it as the formatter for the \Monolog\Handler\StreamHandler 

If you want to use log rotation use the Monolog\Handler\RotatingFileHandler

```
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Mintdev\Monolog\Formatter\SplunkLineFormatter;
use \Monolog\Level;

$log = new Logger('DEMO');
$handler = new StreamHandler('php://stdout', Level::Info);
$handler->setFormatter(new SplunkLineFormatter());
$log->pushHandler($handler);

$log->addError('Bad stuff happened', array('detail1' => 'something', 'detail2' => 'otherthing'));
```

## Thanks to

Part of this project is based on an old project from Vube: https://github.com/vube/monolog-splunk-formatter

It's completely redesigned but many aspects are taken from that project. So thank you!

## Test

Tests are created using Pest

```
./vendor/bin/pest
```

## License

This project is licensed as Open Source under MIT license