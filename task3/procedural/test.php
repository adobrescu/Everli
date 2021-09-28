<?php
    // Do not change number of entries in the following arrays
    // Tests  expected results are based on their length
    $locations = [
        ['id' => 1000, 'zip_code' => '37069', 'lat' => 45.35, 'lng' => 10.84],
        ['id' => 1001, 'zip_code' => '37121', 'lat' => 45.44, 'lng' => 10.99],
        ['id' => 1002, 'zip_code' => '37129', 'lat' => 45.44, 'lng' => 11.00],
        ['id' => 1003, 'zip_code' => '37133', 'lat' => 45.43, 'lng' => 11.02]
      ];
    
    $shoppers = [
        ['id' => 'S1', 'lat' => 45.46, 'lng' => 11.03, 'enabled' => true], // valid locations = 2
        ['id' => 'S2', 'lat' => 45.46, 'lng' => 10.12, 'enabled' => false],
        ['id' => 'S3', 'lat' => 45.34, 'lng' => 10.81, 'enabled' => true], // valid locations = 1
        ['id' => 'S4', 'lat' => 45.76, 'lng' => 10.57, 'enabled' => true], // valid locations = 1
        ['id' => 'S5', 'lat' => 45.34, 'lng' => 10.63, 'enabled' => false],
        ['id' => 'S6', 'lat' => 45.42, 'lng' => 10.81, 'enabled' => true], // valid locations = 2
        ['id' => 'S7', 'lat' => 45.34, 'lng' => 10.94, 'enabled' => true] // valid locations = 1
    ];
    //////////////////////////////
    include_once(__DIR__.'/haversine_coverage.php');
    
    /**
     * Dummy haversine calculator (for testing only).
     * Returns less than, equal or greater than IN_RANGE_MAX_DISTANCE "controlled" values (same value every 3 calls):
     * - num calls % 3 == 0 returns 0
     * - num calls % 3 == 1 returns IN_RANGE_MAX_DISTANCE
     * - num calls % 3 == 2 returns 2 * IN_RANGE_MAX_DISTANCE
     */
    function haversine($lat1, $long1, $lat2, $long2) {
        static $numCalls = 0;
        
        return ( $numCalls++ % 3 ) * IN_RANGE_MAX_DISTANCE;
    }

    
    //tests
    //Note: shopper's coverage is calculated based on shopper's position in array and number of locations
    // (check haversine to see how it generates return values)
    // Given $shoppers and $locations, a shopper can get a coverage of 25 or 50
    // Also, shopper's order change in calculateShoppersCoverage result
    // So all expected values are calculated "manually"

    $result = calculateEnabledShoppersCoverage( $shoppers, $locations);

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
    