# Precision Stopwatch
 
Precisely time PHP scripts and code, down to the nanosecond, by utilizing the [hrtime](https://php.net/hrtime) function. 

## Installing

[Composer](https://getcomposer.org) is the only supported method for installing this library.

```shell
composer require cspray/precision-stopwatch
```

## Usage Guide

Using the provided functionality involves the following steps:

1. Create a new instance of `Cspray\PrecisionStopwatch\Stopwatch`
2. Call `Stopwatch::start()`
3. Do the thing that you're timing!
4. Call `Stopwatch::mark()` (optional, see [_Marking Time_](#marking-time))
5. Call `Stopwatch::stop()`
6. Retrieve information about how long the Stopwatch ran with `Cspray\PrecisionStopwatch\Metrics`

The code examples below can be executed by cloning this repo and running the scripts available in `./examples`.

### Basic Usage

```php
<?php declare(strict_types=1);

use Cspray\PrecisionStopwatch\Stopwatch;

require_once __DIR__ . '/vendor/autoload.php';

$stopwatch = new Stopwatch();

$stopwatch->start();

sleep(3);

$metrics = $stopwatch->stop();

echo 'Duration (ns): ', $metrics->getTotalDuration()->timeTakenInNanoseconds(), PHP_EOL;
echo 'Duration (ms): ', $metrics->getTotalDuration()->timeTakenInMilliseconds(), PHP_EOL;
```

If you execute this example you should see output similar to the following:

```text
% > php ./examples/usage-without-marks.php
Total time taken (ns): 1000584755
Total time taken (ms): 1000.584755
```

### Marking Time

Marking time allows you to retrieve the duration that a Stopwatch has ran to a certain point, while allowing the Stopwatch to continue running. Calling `Stopwatch::mark()` will return a `Cspray\PrecisionStopwatch\Marker` instance. In addition to retrieving the duration up to a certain point, available on the `Marker` instance, you can retrieve the duration between markers with the `Metrics` returned from `Stopwatch::stop()`.

```php
<?php declare(strict_types=1);

use Cspray\PrecisionStopwatch\Stopwatch;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// .75 seconds
$sleepTime = 750_000;

$stopwatch = new Stopwatch();

$stopwatch->start();

usleep($sleepTime);

$mark1 = $stopwatch->mark();

usleep($sleepTime);

$mark2 = $stopwatch->mark();

usleep($sleepTime);

$mark3 = $stopwatch->mark();

usleep($sleepTime);

$metrics = $stopwatch->stop();

echo 'Total time taken (ns): ', $metrics->getTotalDuration()->timeTakenInNanoseconds(), PHP_EOL;
echo 'Total time taken (ms): ', $metrics->getTotalDuration()->timeTakenInMilliseconds(), PHP_EOL;

echo PHP_EOL;

$between1And3 = $metrics->getDurationBetweenMarkers($mark1, $mark3);
echo 'Time take between 1st and 3rd mark (ns): ', $between1And3->timeTakenInNanoseconds(), PHP_EOL;
echo 'Time take between 1st and 3rd mark (ms): ', $between1And3->timeTakenInMilliseconds(), PHP_EOL;

echo PHP_EOL;

echo 'Time taken up to 3rd mark (ns): ', $mark3->getDuration()->timeTakenInNanoseconds(), PHP_EOL;
echo 'Time taken up to 3rd mark (ms): ' , $mark3->getDuration()->timeTakenInMilliseconds(), PHP_EOL;
```

If you execute this example you should see output similar to the following:

```text
% > php ./examples/usage-without-marks.php
Total time taken (ns): 3000608258
Total time taken (ms): 3000.608258

Time take between 1st and 3rd mark (ns): 1500407710
Time take between 1st and 3rd mark (ms): 1500.40771

Time taken up to 3rd mark (ns): 2250497069
Time taken up to 3rd mark (ms): 2250.497069
```
