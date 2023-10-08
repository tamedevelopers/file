<?php

use Tamedevelopers\Support\Env;
use Tamedevelopers\File\AmazonS3;

//on using comoposer autoload
include_once __DIR__  . "/../vendor/autoload.php";


Env::loadOrFail();



// AmazonS3::save(base_path('1644769592c599cf33805896d.jpg'), '1644769592c599cf33805896d');

// AmazonS3::delete('1644769592c599cf33805896d');

// AmazonS3::get('1644769592c599cf33805896d')

dd(
    AmazonS3::get('1644769592c599cf33805896d')
);


