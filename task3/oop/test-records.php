<?php
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