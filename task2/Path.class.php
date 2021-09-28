<?php

class Path
{
    public $currentPath;

    public function __construct($currentPath) {
        $this->currentPath = $currentPath;
    }
    public function cd($relativePath) {
        /**
         * Use a stack to implement the requirement
         * Initially the stack contains only current path components
         * - eg. current path = /a/b/c/d => stack = a, b, c, d
         * 
         * Then foreach givent relative path's component:
         *  - if equals ".." 
         *      - if the stack contains 1 element (the ""/empty component of the current path) 
         *              then top/root directory was reached and cannot move up one level so throw an exception
         *      - else pop the last element from the stack (move one level up in the directory structure)
         *  - if different than ".." then push relative path component in the stack (move to subdirectory)
         *  
         */
        
    }
}