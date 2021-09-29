<?php

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

    protected $shoppersCoveredLocationIds = [];

    public function __construct(\IObservableRepository $shoppers, 
                                \IObservableRepository $locations,
                                $coverageMaxDistance = ShoppersCoverageCalculator::COVERAGE_MAX_DISTANCE) {
        $this->shoppers = $shoppers;
        $this->locations = $locations;
        $this->coverageMaxDistance = $coverageMaxDistance;
        
        $this->shoppers->registerNotificationHandler('create', [$this, 'onCreateShopper']);
        $this->shoppers->registerNotificationHandler('update', [$this, 'onUpdateShopper']);
        $this->shoppers->registerNotificationHandler('delete', [$this, 'onDeleteShopper']);

        $this->locations->registerNotificationHandler('create', [$this, 'onCreateLocation']);
        $this->locations->registerNotificationHandler('update', [$this, 'onUpdateLocation']);
        $this->locations->registerNotificationHandler('delete', [$this, 'onDeleteLocation']);

        foreach ($this->shoppers->readAll() as $shopper ) {
            $this->calculateShopperCoveredLocationIds($shopper);
        }
    }

    public function calculateShopperCoveredLocationIds($shopper) {
        if ( !$shopper['enabled'] ) {
            return;
        }
        $shopperId = $shopper['id'];

        if (!isset($this->shoppersCoveredLocationIds[$shopperId])) {
            $this->shoppersCoveredLocationIds[$shopperId] = [];
        }

        $this->shoppersCoveredLocationIds[$shopperId] = static::calculateShopperCoveredLocationsFromLocations($shopper, $this->locations->readAll(), $this->coverageMaxDistance);
    }

    public function getAllShoppersCoverage() {
        $result = [];
        $numLocations = $this->locations->countAll();
        
        foreach ( $this->shoppersCoveredLocationIds as $shopperId => $coveredLocationIds) {
            $shopperNumCoveredLocations = count($coveredLocationIds);

            $result[] = [ 'shopper_id' => $shopperId, 'coverage' => 100 * $shopperNumCoveredLocations / $numLocations];
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
    static public function calculateShopperCoveredLocationsFromLocations($shopper, $locations, $coverageMaxDistance) {
        $coveredLocations = [];

        foreach ( $locations as $location ) {
            if (!static::isLocationCoveredbyShopper($shopper, $location, $coverageMaxDistance)) {
                continue;
            }
            

            $locationId =  $location['id'];
            $coveredLocations[ $locationId] = $locationId;
        }

        return $coveredLocations;
    }

    static public function isLocationCoveredbyShopper($shopper, $location, $coverageMaxDistance) {
        $distance = static::haversine($shopper['lat'], $shopper['lng'], $location['lat'], $location['lng']);

        if( $distance >= $coverageMaxDistance ) {
            return false;
        }
        return true;
    }

    static public function haversine($lat1, $lng1, $lat2, $lng2) {

    }
    
    // Repository notification handlers
    public function onCreateShopper($shopper) {
        $this->calculateShopperCoveredLocationIds($shopper);
    }

    public function onUpdateShopper($records) {
        $oldShopper = $records[0];
        $shopper = $records[1];

        $shopperId = $shopper['id'];

        if (!$shopper['enabled']) {
            unset($this->shoppersCoveredLocationIds[$shopperId]);
        } elseif (!$oldShopper['enabled'] || // shopper previously disabled
                    //or shopper's position changed
                    $oldShopper['lat'] != $shopper['lat'] || $oldShopper['lng'] != $shopper['lng'] ) {
            $this->calculateShopperCoveredLocationIds($shopper);
        }
    }

    public function onDeleteShopper($shopper) {
        $shopperId = $shopper['id'];
        unset($this->shoppersCoveredLocationIds[$shopperId]);
    }

    public function onCreateLocation($location) {
        foreach ( $this->shoppers->readAll() as $shopper ) {
            if (!$shopper['enabled'] ) {
                continue;
            }

            if (!static::isLocationCoveredbyShopper($shopper, $location, $this->coverageMaxDistance) ) {
                continue;
            }

            $shopperId = $shopper['id'];
            $locationId = $location['id'];

            $this->shoppersCoveredLocationIds[$shopperId][$locationId] = $locationId;
        }
        
    }
    
    public function onUpdateLocation($records) {
        $oldLocation = $records[0];
        $location = $records[1];

        if ( $oldLocation['lat'] == $location['lat'] && 
            $oldLocation['lng'] == $location['lng'] ) {
            //location position didn't change
                return;
            }

        $locationId = $location['id'];

        foreach ( $this->shoppers->readAll() as $shopper ) {
            $shopperId = $shopper['id'];
            
            if ( !isset($this->shoppersCoveredLocationIds[$shopperId]) ) {
                // shopper disbaled ) {
                continue;
            } 
            if (static::isLocationCoveredbyShopper($shopper, $location, $this->coverageMaxDistance) ) {
                //location covered
                $this->shoppersCoveredLocationIds[$shopperId][$locationId] = $locationId;
                continue;
            }
            unset($this->shoppersCoveredLocationIds[$shopperId][$locationId]);
        }
    }

    public function onDeleteLocation($location) {
        $locationId = $location['id'];

        foreach ( $this->shoppers->readAll() as $shopper ) {
            $shopperId = $shopper['id'];
            
            unset($this->shoppersCoveredLocationIds[$shopperId][$locationId]);
        }
    }
}