<?php

namespace Tests\A;

include_once(__DIR__.'/interfaces.php');
include_once(__DIR__.'/NotificationSource.trait.php');
include_once(__DIR__.'/Repository.class.php');
include_once(__DIR__.'/ObservableRepository.class.php');
include(__DIR__.'/test-records.php');

class NotificationSource implements \INotificationSource 
{
    use \TNotificationSource;

    public function test() {
        $this->callNotificationHandlers('someEvent');
    }
}

class ShopperCoverage
{
    public $nearByLocationIds;
}

class ShoppersCoverageCalculator
{
    const COVERAGE_MAX_DISTANCE = 10;
    /*
        Maximum distance a location is considered covered by a shopper;
        Different class instances may use different values;
    */
    protected $coverageMaxDistance; 
    protected $shoppers;
    protected $locations;

    protected $shoppersNearByLocationIds = [];

    public function __construct(\IObservableRepository $shoppers, 
                                \IObservableRepository $locations,
                                $coverageMaxDistance = ShoppersCoverageCalculator::COVERAGE_MAX_DISTANCE) {
        $this->shoppers = $shoppers;
        $this->locations = $locations;
        $this->coverageMaxDistance = $coverageMaxDistance;

        foreach ($this->shoppers->readAll() as $shopper ) {
            $this->calculateShopperNearByLocationIds($shopper);
        }
    }

    public function calculateShopperNearByLocationIds($shopper) {
        if ( !$shopper['enabled'] ) {
            return;
        }
        $shopperId = $shopper['id'];

        if (!isset($this->shoppersNearByLocationIds[$shopperId])) {
            $this->shoppersNearByLocationIds[$shopperId] = [];
        }

        $this->shoppersNearByLocationIds[$shopperId] = static::calculateShopperNearByLocationsFromLocations($shopper, $this->locations->readAll(), $this->coverageMaxDistance);
    }

    public function getAllShoppersCoverage() {
        $result = [];
        $numLocations = $this->locations->countAll();
        
        foreach ( $this->shoppersNearByLocationIds as $shopperId => $nearByLocationIds) {
            $shopperNumNearByLocations = count($nearByLocationIds);

            $result[] = [ 'shopper_id' => $shopperId, 'coverage' => 100 * $shopperNumNearByLocations / $numLocations];
        }

        usort($result, [$this, 'static::compareShoppersCoverageDesc']);

        return $result;
    }

    static public function compareShoppersCoverageDesc($shopper1, $shopper2) {
        if ( $shopper1['coverage'] > $shopper2['coverage'] ) {
            return -1;
        }
        if ( $shopper1['coverage'] < $shopper2['coverage'] ) {
            return 1;
        }
        return 0;
    }
    /**
     * Given a shopper, a list of locations and a range maximum distance,
     * returns a list of locations that are in shopper's coverage.
     */
    static public function calculateShopperNearByLocationsFromLocations($shopper, $locations, $coverageMaxDistance) {
        $nearByLocations = [];

        foreach ( $locations as $location ) {
            $distance = static::haversine($shopper['lat'], $shopper['lng'], $location['lat'], $location['lng']);

            if( $distance >= $coverageMaxDistance ) {
                continue;
            }

            $locationId =  $location['id'];
            $nearByLocations[ $locationId] = $locationId;
        }

        return $nearByLocations;
    }
    static public function haversine($lat1, $lng1, $lat2, $lng2) {

    }
}

/**
 * Test class, just overrides the 'haversine' method
 */

class TestShoppersCoverageCalculator extends ShoppersCoverageCalculator
{
    static public function haversine($lat1, $lng1, $lat2, $lng2) {
        static $numCalls = 0;
        
        return ( $numCalls++ % 3 ) * ShoppersCoverageCalculator::COVERAGE_MAX_DISTANCE;
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
$calculator = new TestShoppersCoverageCalculator(
    new \ObservableRepository($shoppers),
    new \ObservableRepository($locations)
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

echo 'No failed assertions' . PHP_EOL;

