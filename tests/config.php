<?php

include_once __DIR__  . "/../vendor/autoload.php";

// Configure the file uploader using global function helper
config_file(
    message: [
        '401'   => 'Select file to upload',
        '402'   => 'File upload is greater than allowed size of:',
        '403'   => 'Maximum file upload exceeded. Limit is:',
        '404'   => 'Uploaded file format not allowed! allowed format is:',
        '405'   => 'Image dimension allowed is:',
        '405x'  => 'Image dimension should be greater than or equal to:',
        '200'   => 'File uploaded successfully:',
        'kb'    => 'kb',
        'mb'    => 'mb',
        'gb'    => 'gb',
        'and'   => 'and',
        'width' => 'width',
        'height'=> 'height',
        'files' => 'files',
        'file'  => 'file',
    ],
    config: [
        'limit'         => 1,
        'mime'          => 'image', // video|audio|file|image|zip|pdf|xls|doc|general_image|general_media|general_file
        'size'          => 2097152, // 2mb
        'baseDir'       => 'public',
        'driver'        => 'local',
        'structure'     => 'default', // default|year|month|day
        'generate'      => true, // will always generate a unique() name for each uploaded file
    ],
    class: [
        'error'     => 'bg-danger',
        'success'   => 'bg-success',
    ]
); 

