<?php declare(strict_types=1);

use Cspray\PrecisionStopwatch\Stopwatch;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$stopwatch = new Stopwatch();

$stopwatch->start();
// 1.5 seconds
usleep(1_000_500);

$metrics = $stopwatch->stop();

echo 'Total time taken (ns): ', $metrics->getTotalDuration()->timeTakenInNanoseconds(), PHP_EOL;
echo 'Total time taken (ms): ', $metrics->getTotalDuration()->timeTakenInMilliseconds(), PHP_EOL;