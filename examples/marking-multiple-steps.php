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

echo 'Time take between 1st and 3rd mark (ns): ', $metrics->getDurationBetweenMarkers($mark1, $mark3)->timeTakenInNanoseconds(), PHP_EOL;
echo 'Time take between 1st and 3rd mark (ms): ', $metrics->getDurationBetweenMarkers($mark1, $mark3)->timeTakenInMilliseconds(), PHP_EOL;

echo PHP_EOL;

echo 'Time taken up to 3rd mark (ns): ', $mark3->getDuration()->timeTakenInNanoseconds(), PHP_EOL;
echo 'Time taken up to 3rd mark (ms): ' , $mark3->getDuration()->timeTakenInMilliseconds(), PHP_EOL;
