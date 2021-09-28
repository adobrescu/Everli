<?php

class Path
{
    const ERR_INVALID_PATH = 2001;

    public $currentPath;

    public function __construct($currentPath) {
        $this->currentPath = $currentPath;
    }
    public function cd($relativePath) {
        $currentPath = $this->currentPath;

        /**
         * If $relativePath starts  with a "/" then set currentPath to "/" and treat the remaining part as 
         * a relative path.
        */

        if ( $relativePath[0] == '/' ) {
            $currentPath = '/';
            $relativePath = substr($relativePath, 0);
        } else {
            $currentPath = $this->currentPath;
        }
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
        if ( $currentPath == '/' ) {
            $stack = [''];
        } else {
            $stack = explode('/', $currentPath);
        }
        $relativePathComponents = explode('/', $relativePath);
        
        foreach ( $relativePathComponents as $relativePathComponent) {
            switch ( $relativePathComponent ) {
                case '.':
                case '':
                    break;
                case '..':
                    //move one level up if possible, otherwise throw an exception
                    if ( count($stack) == 1 ) {
                        //top level reached
                        throw new Exception('Root / top level reached', static::ERR_INVALID_PATH);
                    }
                    array_pop($stack);
                    break;
                default:
                    array_push($stack, $relativePathComponent);
            }
        }
        
        if ( count($stack) == 1 ) {
            // the stack contains the empty element only
            $this->currentPath = '/';
        } else {
            $this->currentPath = implode('/', $stack);
        }
    }
}