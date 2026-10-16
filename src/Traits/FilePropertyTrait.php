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
     * File Bag Data
     *
     * @var null|\Tamedevelopers\Support\Capsule\FileBag
     */
    protected $file;

    /**
     * Cache of created instances by field name
     * 
     * @var array<string, static>
     */
    protected static $instances = [];

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
    protected $success = false;
    
    /**
     * Keep track of manually calling the validate method
     *
     * @var bool
     */
    protected $isValidatedCalled = false;
    
    /**
     * All available drivers
     * @var array
     */
    public $drivers = [
        'local' => File::class,
        's3'    => AmazonS3::class,
    ];
    
    /**
     * Data response
     * @var array
     */
    protected $data = [
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
        'label'     => null,
        'mime'      => 'image',
        'structure' => null,
        'generate'  => true,
        'limitText' => true,
    ];

    /**
     * Private Errors for internal usage
     * @var array
     */
    protected $error = [
        '401' => 401,
        '402' => 402,
        '403' => 403,
        '404' => 404,
        '405' => 405,
        '200' => 200
    ];

    /**
     * Css Class Error handler
     * @var array
     */
    protected $class = [
        'error'     => 'bg-danger',
        'success'   => 'bg-success',
    ];

    /**
     * Cached Configuration
     * @var array
     */
    protected static $cachedConfig = null;

}