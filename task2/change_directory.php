#!/usr/bin/env php
<?php

    include_once(__DIR__.'/Path.class.php');

    $path = new Path('/a/b/c/d');
    assert($path->currentPath == '/a/b/c/d');

    $path->cd('../x');
    
    assert($path->currentPath == '/a/b/c/x');
    //should display '/a/b/c/x'. 