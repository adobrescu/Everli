<?php

class Path
{
    const ERR_INVALID_PATH = 2001;
    const ERR_INVALID_PATH_FORMAT = 2002;

    public $currentPath;

    public function __construct($currentPath) {
        $this->currentPath = $currentPath;
    }
    public function cd($relativePath) {
        //not clear if "the function will not be passed any invalid paths"
        // also refers to "directory names consist only of English alphabet letters (A-Z and a-z).
        //$relativePath = static::sanitizeRelativePath($relativePath);

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

    /**
     * Sanitize a relative path. On error throws an ERR_INVALID_PATH_FORMAT exception.
     * returns sanitized path
     */
    static public function sanitizeRelativePath($relativePath) {
        //remove multiple '/'
        $relativePath = preg_replace('/[\/]{2,}/', '/', $relativePath);

        //remove trailing '/'
        $relativePath = preg_replace('/[\/]$/', '', $relativePath);

        // path component allows a-z characters, ".." (2 dots) and "." ("one dot)
        $rpcp /*relativePathComponentPattern*/ = '\.|\.\.|[a-z]+';

        //path pattern starts with 0 - 1 path component followed by one or more 
        // '/' followed by a path component
        $relativePathPattern = '^('.$rpcp.'){0,1}(\/('.$rpcp.'))*$';
        if ( !preg_match('/'.$relativePathPattern.'/i', $relativePath)) {
            throw new Exception('Invalid path format: '.$relativePath, static::ERR_INVALID_PATH_FORMAT);
        }
        
        return $relativePath;
    }
}