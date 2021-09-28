<?php

const IN_RANGE_MAX_DISTANCE = 10; // km

function calculateEnabledShoppersCoverage($shoppers, $locations) {
        
    $numLocations = count($locations);
    $result = [];

    foreach ( $shoppers as $shopper ) {

        if (!$shopper['enabled']) {
            //skip disabled shoppers
            continue;
        }
        $numInRangeLocations = 0;

        foreach ( $locations as $location ) {
            $d = haversine($shopper['lat'], $shopper['lng'], $location['lat'], $location['lng']);

            if ( $d >= IN_RANGE_MAX_DISTANCE ) {
                continue;
            }

            $numInRangeLocations++;
        }
        $coverage = 100 * $numInRangeLocations / $numLocations;
        $result[] = ['shopper_id' => $shopper['id'],
                        'coverage' =>  $coverage];
    }

    usort($result, 'compareShoppersCoverageDesc');

    return $result;
}
function compareShoppersCoverageDesc($shopper1, $shopper2) {
    
    if ( $shopper1['coverage'] > $shopper2['coverage'] ) {
        return -1;
    }
    if ( $shopper1['coverage'] < $shopper2['coverage'] ) {
        return 1;
    }
    return 0;
}