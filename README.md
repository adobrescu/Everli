# Everli

TASK 1  
  
!! My notes  
  
Task 1 implements 2 versions of a function called "reverseBinary" - one implemented using bitwise operators (in reverse_binary.js), and the second one using strings and arrays (reverse_binary_using_arrays.js)  
  
To run the tests from command line (assuming nodejs installed):  
  
cd Everli/task1/tests/ ; node ./tests.js ; node ./tests_arrays.js  
  
To run in browser open Everli/task1/index.html and view results in console output. 
   
!!  
  
- Language:  
Javascript  
  
- Description:  
Write a function for reversing numbers in binary. For instance, the binary representation of 13 is 1101, and reversing it gives 1011, which corresponds to number 11.  
  
- How to submit:  
Complete the source code file named `reverse_binary.js`.


TASK 2  
  
!! My notes  

Path class definition can be found in change_directory.php:  
  
To run the tests (assuming Everli/task2/tests/test.php has execution permissions and PHP CLI installed):  

cd Everli/task2/tests ; ./test.php;  
  
Or (assuming that PHP CLI installed and in PATH):  
  
cd Everli/task2/tests ; php ./test.php;  
  
!!  
  
- Language: PHP  
  
- Description:  
Write a function that provides change directory (cd) function for an abstract file system.  
Notes:  
- root path is '/'.  
- path separator is '/'.  
- parent directory is addressable as '..'.  
- directory names consist only of English alphabet letters (A-Z and a-z).  
the function will not be passed any invalid paths.  
- do not use built-in path-related functions.  
  
- For example:  
$path = new Path('/a/b/c/d');  
$path->cd('../x');  
echo $path->currentPath;  
//should display '/a/b/c/x'.  
  
- How To Submit:  
Complete the source file named `change_directory.php`.  
  
TASK 3  
  
!! My notes  
  
1. One simple implementation is found in task3/procedural/haversine_coverage.php.  
The function that implements coverages calculation (calculateEnabledShoppersCoverage) is done in a "if it fails, it fails hard" way:  
  
- it doesn't check its input/parameters for their type (array) 
   or their entries (array elements have the right keys and values);  
- it expects that a function called 'haversine' is already defined;  
  
2. A second one, OOP oriented using classes and interfaces, has multiple source files found in task3/oop/.  
The requirement is implemented in ShoppersCoverageCalculator class and exposed by ShoppersCoverageCalculator::getAllShoppersCoverage public method.  
  
The requirement is implemented by a few classes by following repository-like and event driven-like patterns.   

!!  
  
- Language: PHP  
  
- Description:  
Suppose you have:  
- a `haversine(lat1, lng1, lat2, lng2)` function that returns the distance (measured in km) between the coordinates of two given geographic point (lat and lng are latitude and longitude)
an array of geographical zones (`locations`):  
	$locations = [  
    	  ['id' => 1000, 'zip_code' => '37069', 'lat' => 45.35, 'lng' => 10.84],  
    	  ['id' => 1001, 'zip_code' => '37121', 'lat' => 45.44, 'lng' => 10.99],  
    	  ['id' => 1002, 'zip_code' => '37129', 'lat' => 45.44, 'lng' => 11.00],  
          ['id' => 1003, 'zip_code' => '37133', 'lat' => 45.43, 'lng' => 11.02],  
  ...   
    	];  
  
- an array of shoppers:  
  
$shoppers = [  
    ['id' => 'S1', 'lat' => 45.46, 'lng' => 11.03, 'enabled' => true],  
    ['id' => 'S2', 'lat' => 45.46, 'lng' => 10.12, 'enabled' => true],  
    ['id' => 'S3', 'lat' => 45.34, 'lng' => 10.81, 'enabled' => true],  
    ['id' => 'S4', 'lat' => 45.76, 'lng' => 10.57, 'enabled' => true],  
    ['id' => 'S5', 'lat' => 45.34, 'lng' => 10.63, 'enabled' => true],  
    ['id' => 'S6', 'lat' => 45.42, 'lng' => 10.81, 'enabled' => true],  
    ['id' => 'S7', 'lat' => 45.34, 'lng' => 10.94, 'enabled' => true],  
];  
  
- The goal is to calculate the percentage of the zone covered by enabled shoppers (`coverage`). One shopper covers a zone if the distance among the coordinates is less than 10 km.  
Resulted array should be sorted (desc) as the following one:  
$sorted = [  
  [shopper_id' => 'S3', 'coverage' => 72],  
  [shopper_id' => 'S1', 'coverage' => 43],  
  [shopper_id' => 'S6', 'coverage' => 12],  
];  
- How to submit:  
Complete the source code file named `haversine_coverage.php`.  

