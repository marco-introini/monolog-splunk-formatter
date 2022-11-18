<?php

use Mintdev\Monolog\Formatter\SplunkLineFormatter;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;

use function Pest\Faker\faker;

beforeEach(function (){
    $this->splunkFormatter = new SplunkLineFormatter();
    $this->handler = new TestHandler(Level::Debug);
    $this->handler->setFormatter($this->splunkFormatter);
    $log = new Logger('test');
    $log->pushHandler($this->handler);
});

test('a basic string works with no conversions', function () {
    $str = faker()->text;
    $converted = $this->splunkFormatter->toString($str);
    expect($converted)->toEqual($str);
});
