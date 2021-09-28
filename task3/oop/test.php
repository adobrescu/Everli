<?php

namespace Tests\A;

include_once(__DIR__.'/interfaces.php');
include_once(__DIR__.'/NotificationSource.trait.php');
include(__DIR__.'/test-records.php');

class NotificationSource implements \INotificationSource 
{
    use \TNotificationSource;

    public function test() {
        $this->callNotificationHandlers('someEvent');
    }
}

class Repository implements \IRepository
{
    protected $records;

    /**
     * Creates repo object with records from a trusted source
     */
    public function __construct($records) {
        $this->records = $records;
    }

    /**
     * Adds new record from trusted source
     */
    function createRecord(array $record): int {
    }
    function deleteRecord(int $id) {

    }

    function readAll() {
        return $this->records;
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
$repo = new Repository($locations);
assert($numLocations == count($repo->readAll()));


