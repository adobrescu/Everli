<?php

namespace Tests\A;

include_once(__DIR__.'/interfaces.php');
include_once(__DIR__.'/NotificationSource.trait.php');

class NotificationSource implements \INotificationSource 
{
    use \TNotificationSource;

    public function test() {
        $this->callNotificationHandlers('someEvent');
    }
}

class Repository implements \IRepository
{
    function createRecord(array $record) {

    }
    function deleteRecord(integer $id) {

    }

    function readAll() {
        
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
