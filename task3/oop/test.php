<?php

namespace Tests\A;

include_once(__DIR__.'/interfaces.php');
include_once(__DIR__.'/NotificationSource.trait.php');
include_once(__DIR__.'/Repository.class.php');
include_once(__DIR__.'/ObservableRepository.class.php');
include_once(__DIR__.'/ShoppersCoverageCalculator.class.php');
include(__DIR__.'/test-records.php');

class NotificationSource implements \INotificationSource 
{
    use \TNotificationSource;

    public function test() {
        $this->callNotificationHandlers('someEvent');
    }
}



/**
 * Test class, just overrides the 'haversine' method
 */

class TestShoppersCoverageCalculator extends \ShoppersCoverageCalculator
{
    /*
        $reset allows resetting  number of calls counter - testing purposes
    */

    /* controls haversine return value - if not null, haversine always return this value */
    static public $haversineReturnValue = null;

    static public function haversine($lat1, $lng1, $lat2, $lng2, $reset = false) {
        static $numCalls = 0;
                
        if ($reset ) {
            $numCalls = 0;
            return;
        }
        if (!is_null(static::$haversineReturnValue)) {
            return static::$haversineReturnValue;
        }
        return ( $numCalls++ % 3 ) * TestShoppersCoverageCalculator::COVERAGE_MAX_DISTANCE;
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

$repo = new \ObservableRepository($shoppers);
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

//ShoppersCoverageCalculator tests
$shoppersRepo = new \ObservableRepository($shoppers);
$locationsRepo = new \ObservableRepository($locations);

$calculator = new TestShoppersCoverageCalculator(
    $shoppersRepo,
    $locationsRepo
);
$result = $calculator->getAllShoppersCoverage();

assert(5 == count($result));//7 shoppers - 2 disabled

assert($result[0]['shopper_id'] == 'S1' || $result[0]['shopper_id'] == 'S6' );
assert($result[1]['shopper_id'] == 'S1' || $result[1]['shopper_id'] == 'S6' );

assert($result[2]['shopper_id'] == 'S3' || $result[0]['shopper_id'] == 'S4'  || $result[0]['shopper_id'] == 'S7');
assert($result[3]['shopper_id'] == 'S3' || $result[3]['shopper_id'] == 'S4'  || $result[3]['shopper_id'] == 'S7');
assert($result[4]['shopper_id'] == 'S3' || $result[4]['shopper_id'] == 'S4'  || $result[4]['shopper_id'] == 'S7');

assert(50 == $result[0]['coverage']);
assert(50 == $result[1]['coverage']);

assert(25 == $result[2]['coverage']);
assert(25 == $result[3]['coverage']);
assert(25 == $result[4]['coverage']);

// Test ShoppersCoverageCalculator repos notification handlers
//add a new enabled shopper
//getAllShoppersCoverage must return one more entry in its result

//reset # of calls counter
TestShoppersCoverageCalculator::haversine(0, 0, 0, 0, true);

$newShopperId = $shoppersRepo->createRecord(['id' => 'S8', 'lat' => 45.46, 'lng' => 11.03, 'enabled' => true]);
$result = $calculator->getAllShoppersCoverage();
assert(6 == count($result));// one more entry in result

// disable last added shopper, $result must have one entry less
$shoppersRepo->updateRecord($newShopperId, ['id' => 'S8', 'lat' => 45.46, 'lng' => 11.03, 'enabled' => false]);
$result = $calculator->getAllShoppersCoverage();
assert(5 == count($result));// one entry less in result

//enable back the shopper, $result must have 6 entries again
$shoppersRepo->updateRecord($newShopperId, ['id' => 'S8', 'lat' => 45.46, 'lng' => 11.03, 'enabled' => true]);
$result = $calculator->getAllShoppersCoverage();
assert(6 == count($result));// one more entry in result

//change shopper's position, $result must have 6 entries again
// set all location in all shoppers coverage areas
TestShoppersCoverageCalculator::$haversineReturnValue = 0;
$shoppersRepo->updateRecord($newShopperId, ['id' => 'S8', 'lat' => 45.46, 'lng' => 11.33, 'enabled' => true]);
$result = $calculator->getAllShoppersCoverage();
assert(6 == count($result));// one more entry in result

foreach ($result as $shopperCoverage ) {
    if ( $shopperCoverage['shopper_id'] == 'S8' ) {
        assert(100 == $shopperCoverage['coverage']);
    }
}

// delete last added shopper, $result must have one entry less
$shoppersRepo->deleteRecord($newShopperId);
$result = $calculator->getAllShoppersCoverage();
assert(5 == count($result));

// add new location
TestShoppersCoverageCalculator::$haversineReturnValue = 0; //location in coverage for all shoppers
$newLocationKey = $locationsRepo->createRecord(['id' => 1004, 'zip_code' => '37133', 'lat' => 45.43, 'lng' => 11.02]);
$result = $calculator->getAllShoppersCoverage();

assert(60 == $result[0]['coverage']); // was 2/4 , now 3/5
assert(60 == $result[1]['coverage']);

assert(40 == $result[2]['coverage']); //was 1/4, now 2/5
assert(40 == $result[3]['coverage']);
assert(40 == $result[4]['coverage']);




// change last location position
// it is out of coverage area for all shoppers
TestShoppersCoverageCalculator::$haversineReturnValue = 2 * TestShoppersCoverageCalculator::COVERAGE_MAX_DISTANCE;
$locationsRepo->updateRecord($newLocationKey, ['id' => 1004, 'zip_code' => '37133', 'lat' => 45.13, 'lng' => 11.02]);
$result = $calculator->getAllShoppersCoverage();

assert(40 == $result[0]['coverage']); // was  3/5, now 2/5
assert(40 == $result[1]['coverage']);

assert(20 == $result[2]['coverage']); //was 2/5, now 1/5
assert(20 == $result[3]['coverage']);
assert(20 == $result[4]['coverage']);


// change again last location position
// it is in coverage area of all shoppers
TestShoppersCoverageCalculator::$haversineReturnValue = 0;
$locationsRepo->updateRecord($newLocationKey, ['id' => 1004, 'zip_code' => '37133', 'lat' => 45.03, 'lng' => 11.12]);
$result = $calculator->getAllShoppersCoverage();

assert(60 == $result[0]['coverage']); // was  2/5, now 3/5
assert(60 == $result[1]['coverage']);

assert(40 == $result[2]['coverage']); //was 1/5, now 2/5
assert(40 == $result[3]['coverage']);
assert(40 == $result[4]['coverage']);

//delete last position
$locationsRepo->deleteRecord($newLocationKey);
$result = $calculator->getAllShoppersCoverage();

assert(50 == $result[0]['coverage']); // was 3/5 , now 2/4
assert(50 == $result[1]['coverage']);

assert(25 == $result[2]['coverage']); //was 2/5, now 1/4
assert(25 == $result[3]['coverage']);
assert(25 == $result[4]['coverage']);


echo 'No failed assertions' . PHP_EOL;

