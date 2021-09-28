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

class ObservableRepository extends \Repository implements \INotificationSource, \IRepository
{
    use \TNotificationSource;
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

