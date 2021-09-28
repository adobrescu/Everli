#!/usr/bin/env php
<?php

    include_once(__DIR__.'/Path.class.php');

    $path = new Path('/a/b/c/d');
    assert($path->currentPath == '/a/b/c/d');

    $path->cd('../x');
    assert($path->currentPath == '/a/b/c/x');

    try {
        $path->cd('../../../../../aa');
        assert(false, 'Exception not thrown');
    } catch (Exception $err) {
        assert($err->getCode() == Path::ERR_INVALID_PATH);
        assert($path->currentPath == '/a/b/c/x');
    }
    
    $path->cd('../../../../a');
    assert('/a' == $path->currentPath);

    $path->cd('../');
    assert('/' == $path->currentPath);

    $path->cd('a');
    assert('/a' == $path->currentPath);

    $path->cd('.');
    assert('/a' == $path->currentPath);

    $path->cd('./b/c');
    assert('/a/b/c' == $path->currentPath);

    $path->cd('./b/c/');
    assert('/a/b/c/b/c' == $path->currentPath);

    $path->cd('./b/c/../../../../../../x/y');
    assert('/a/x/y' == $path->currentPath);
    //should display '/a/b/c/x'. 