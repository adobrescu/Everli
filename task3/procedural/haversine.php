<?php
    // Formula taken from:
    // https://www.vcalc.com/wiki/vCalc/Haversine+-+Distance

    const EARTH_MEAN_RADIUS_KM = 6367;
    /**
     * Calculates haversine distance between 2 points, given their latitude and longitude in radians
     * 
     */
    function haversine($lat1, $lng1, $lat2, $lng2) {
        
        $lat1Radian = M_PI * $lat1 / 180;
        $lng1Radian = M_PI * $lng1 / 180;

        $lat2Radian = M_PI * $lat2 / 180;
        $lng2Radian = M_PI * $lng2 / 180;

        $dlng = $lng2Radian - $lng1Radian;
        $dlat = $lat2Radian - $lat1Radian;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1Radian) * cos($lat2Radian) * sin($dlng/2) * sin($dlng/2);
        
        $distance = 2 * EARTH_MEAN_RADIUS_KM * asin(sqrt($a));

        return $distance;
    }