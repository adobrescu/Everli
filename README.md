# Everli

TASK 1
!! My notes 
Task 1 implements 2 versions of a function called "reverseBinary" - one implemented using bitwise operators (in reverse_binary.js), and the second one using strings and arrays (reverse_binary_using_arrays.js)
    
    To run the tests from command line (assuming nodejs installed):

    cd Everli/task1/tests/ ; node ./tests.js ; node ./tests_arrays.js

    To run in browser open Everli/task1/index.html and view results in console output.
!!    

- Language: Javascript

- Description:
Write a function for reversing numbers in binary. For instance, the binary representation of 13 is 1101, and reversing it gives 1011, which corresponds to number 11.

- How to submit: 
Complete the source code file named `reverse_binary.js`.


TASK 2

!! My notes 
    To run the code (class definition + tests) if change_directory.php has exec permissions:

    cd Everli/task2/ ; ./change_directory.php;

    Or (assuming that PHP CLI installed and in PATH):

    cd Everli/task2/ ; php ./change_directory.php;


!!

- Language: PHP

- Description:
Write a function that provides change directory (cd) function for an abstract file system.
Notes:
root path is '/'.
path separator is '/'.
parent directory is addressable as '..'.
directory names consist only of English alphabet letters (A-Z and a-z).
the function will not be passed any invalid paths.
do not use built-in path-related functions.

- For example:
$path = new Path('/a/b/c/d');
$path->cd('../x');
echo $path->currentPath;
should display '/a/b/c/x'.

- How To Submit:
Complete the source file named `change_directory.php`.
