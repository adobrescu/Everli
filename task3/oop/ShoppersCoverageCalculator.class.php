<?php

/**
 * ShoppersCoverageCalculator implements task 3 requirements.

 */
class ShoppersCoverageCalculator
{
    const COVERAGE_MAX_DISTANCE = 10;
    /**
     * @property int $coverageMaxDistance
     * Maximum distance a location is considered covered by a shopper.
     * It defaults to COVERAGE_MAX_DISTANCE, but different class instances may use different values.
     */
    protected $coverageMaxDistance; 

    /**
     * @property \IObservableRepository $shoppers
     * Shoppers repository
     */
    protected $shoppers;

    /**
     * @property \IObservableRepository $locations
     * Locations repository
     */
    protected $locations;

    /**
     * @property array $shoppersCoveredLocationIds
     * Caches already calculated shoppers coverage.
     * Gets initialised in constructor, and updated in repository notification handlers
     */
    protected $shoppersCoveredLocationIds = [];

    /**
     * Constructor.
     * Initialize object properties and shoppers coverage cache 
     */
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

    /**
     * Calculates a given shopper coverage based on all locations.
     * 
     * @param array $shopper
     */
    protected function calculateShopperCoveredLocationIds($shopper) {
        if ( !$shopper['enabled'] ) {
            return;
        }
        $shopperId = $shopper['id'];

        if (!isset($this->shoppersCoveredLocationIds[$shopperId])) {
            $this->shoppersCoveredLocationIds[$shopperId] = [];
        }

        $this->shoppersCoveredLocationIds[$shopperId] = static::calculateShopperCoveredLocationsFromLocations($shopper, $this->locations->readAll(), $this->coverageMaxDistance);
    }

    /**
     * Main method exposed by the class, it returns enabled/active shoppers coverage.
     * 
     * @tbd this method return value could be also cached 
     * 
     * @return array $result An array with a ['shopper_id' => ..., 'coverage' => ... ] format
     */
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

    /**
     * Util method.
     * Used to compare 2 shopper coverages when sorting
     * coverages desc
     * 
     * @param array $shopperCoverage1
     * @param array $shopperCoverage2
     * 
     * @return Comparison desc style (-1,0, 1) result
     */
    static public function compareShoppersCoverageDesc($shopperCoverage1, $shopperCoverage2) {
        if ( $shopperCoverage1['coverage'] > $shopperCoverage2['coverage'] ) {
            return -1;
        }
        if ( $shopperCoverage1['coverage'] < $shopperCoverage2['coverage'] ) {
            return 1;
        }
        return 0;
    }
    /**
     * Util method.
     * Given a shopper, a list of locations and a range maximum distance,
     * returns a list of locations that are in shopper's coverage.
     * 
     * @param array $shopper
     * @param array $locations List of locations
     * @param float $coverageMaxDistance 
     * 
     * @return array List of covered locations
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

    /**
     * Util method.
     * Given a shopper, a location and a range maximum distance,
     * it calculates if the location is in shopper's coverage area.
     * 
     * @param array $shopper
     * @param array $location
     * @param float $coverageMaxDistance
     * 
     * @return boolean True if the location is covered by the shopper, otherwise false
     */
    static public function isLocationCoveredbyShopper($shopper, $location, $coverageMaxDistance) {
        $distance = static::haversine($shopper['lat'], $shopper['lng'], $location['lat'], $location['lng']);

        if( $distance >= $coverageMaxDistance ) {
            return false;
        }
        return true;
    }

    /**
     * Calculates and returns the distance between 2 points, given their coordinates
     * 
     * @param  float $lat1 1st point's latitude
     * @param  float $lng1 1st point's longitude
     * @param  float  $lat2 2nd point's latitude
     * @param  float  $lng2 2nd point's longitude
     * 
     * @return float The distance between 2 points
     */
    static public function haversine($lat1, $lng1, $lat2, $lng2) {

    }
    
    // Repository notification handlers
    /**
     * Called when a new shopper is added to the shoppers repository,
     * calculates new shopper's covered locations based on all locations.
     */
    public function onCreateShopper($shopper) {
        $this->calculateShopperCoveredLocationIds($shopper);
    }

    /**
     * Called when an existing shopper is updated.
     * 
     * If the shopper is disabled then it removes shopper from cached covered locations.
     * Or, if the shopper is enabled and was previously disabled or their position has changed
     * then re-calculate shopper's covered locations based on all locations
     */
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

    /**
     * Called when a shopper is removed from repository, it just removes the shopper 
     * from cached shopper covered locations.
     */
    public function onDeleteShopper($shopper) {
        $shopperId = $shopper['id'];
        unset($this->shoppersCoveredLocationIds[$shopperId]);
    }

    /**
     * Called whe a new location is added to repository.
     * For each enabled shopper, checks if the location is covered 
     * and updates shopper covered locations accordingly.
     */
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
    /**
     * Called when an existing location changes.
     * If location position has changed then for each enabled shopper, 
     * checks if the location is covered and updates shopper covered locations accordingly.
     */
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
    /**
     * Called when a location is deleted from repository.
     * Just removes the location from shopper covered locations cache.
     */
    public function onDeleteLocation($location) {
        $locationId = $location['id'];

        foreach ( $this->shoppers->readAll() as $shopper ) {
            $shopperId = $shopper['id'];
            
            unset($this->shoppersCoveredLocationIds[$shopperId][$locationId]);
        }
    }
}