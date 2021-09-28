#!/usr/bin/env php
<?php

    $path = new Path('/a/b/c/d');
    $path->cd('../x');
    
    assert($path->currentPath == '/a/b/c/x');
    //should display '/a/b/c/x'. 