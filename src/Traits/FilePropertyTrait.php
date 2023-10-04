<?php

namespace Tamedevelopers\File\Traits;

use Tamedevelopers\File\File;
use Tamedevelopers\File\AmazonS3;


trait FilePropertyTrait{
    
    /**
     * File input name
     *
     * @var string
     */
    protected $name;

    /**
     * File data
     *
     * @var mixed
     */
    protected $files;

    /**
     * File Uploaded Data
     *
     * @var mixed
     */
    public $uploads;
    
    /**
     * Destination source
     * @var string
     */
    public $folder;
    
    /**
     * Success response 
     *
     * @var bool
     */
    private $success = false;
    
    /**
     * All available drivers
     * @var array
     */
    public $driverTypes = [
        'local' => File::class,
        's3'    => AmazonS3::class,
    ];
    
    /**
     * Data response
     * @var array
     */
    private $data = [
        'message'   => '',
        'status'    => 0,
    ];

    /**
     * internal build property
     * @var array
     */
    public $config = [
        'driver'    => 'local',
        'baseUrl'   => null,
        'baseDir'   => null,
        'limit'     => 1,
        'width'     => null,
        'height'    => null,
        'size'      => null,
        'mime'      => 'images',
        'structure' => null,
        'generate'  => true,
    ];

    /**
     * Private Errors for internal usage
     * @var array
     */
    private $error = [
        '401' => 401,
        '402' => 402,
        '403' => 403,
        '404' => 404,
        '405' => 405,
        '200' => 200
    ];
}