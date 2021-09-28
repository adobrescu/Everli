<?php

namespace Tests\A;

include_once(__DIR__.'/interfaces.php');
include_once(__DIR__.'/NotificationSource.trait.php');
include_once(__DIR__.'/Repository.class.php');
include(__DIR__.'/test-records.php');

class NotificationSource implements \INotificationSource 
{
    use \TNotificationSource;

    public function test() {
        $this->callNotificationHandlers('someEvent');
    }
}



$obj = new NotificationSource();
$obj->registerNotificationHandler('someEvent', function () {
    global $testVar;
    $testVar = 1;
});
$testVar = 0;
$obj->test(); 

assert($testVar == 1);

/////////////////////////////////

$numLocations = count($locations);
$repo = new \Repository($locations);
assert($numLocations == count($repo->readAll()));

$newRecordKey = $repo->createRecord(['id' => 1100, 'zip_code' => '37069', 'lat' => 45.35, 'lng' => 10.84]);
assert($numLocations + 1 == count($repo->readAll()));

$repo->deleteRecord($newRecordKey);
assert($numLocations == count($repo->readAll()));

/////////////////////////////////

$repo = new ObservableRepository($shoppers);
$repo->registerNotificationHandler('create', function () {
    global $numRecords;

    $numRecords ++;
});

$repo->registerNotificationHandler('delete', function () {
    global $numRecords;

    $numRecords --;
});

$numRecords0 = $numRecords = count($shoppers);

$repo->createRecord(['id' => 'S1', 'lat' => 45.46, 'lng' => 11.03, 'enabled' => true]);
$lastNewKey = $repo->createRecord(['id' => 'S1', 'lat' => 45.46, 'lng' => 11.03, 'enabled' => true]);

assert($numRecords0 + 2 == $numRecords);

$repo->deleteRecord($lastNewKey);

assert($numRecords0 + 1 == $numRecords);

echo 'No failed assertions' . PHP_EOL;

