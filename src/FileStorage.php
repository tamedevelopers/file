<?php

namespace Tamedevelopers\File;

use Exception;
use Tamedevelopers\Support\Env;
use Tamedevelopers\Support\Tame;
use Tamedevelopers\File\Methods\FileMethod;
use Tamedevelopers\File\Traits\CommonTrait;
use Tamedevelopers\Support\Capsule\Manager;
use Tamedevelopers\File\Traits\FileStorageTrait;
use Tamedevelopers\Support\Capsule\CustomException;

class FileStorage extends FileMethod{

    use FileStorageTrait, CommonTrait;

    /**
     * Upload Input name
     *
     * @var string|null
     */
    private $name;

    /**
     * Upload Input name
     *
     * @var string|null
     */
    private $folder;

    /**
     * File
     *
     * @var mixed
     */
    private $file;
    
    /**
     * Upload Data
     *
     * @var array|null
     */
    private $config;

    /**
     * Driver Types
     *
     * @var array|null
     */
    private $driverTypes;
    
    
    /**
     * __construct
     *
     * @param  mixed $file
     * @param  mixed $config
     * @param  mixed $name
     * @param  mixed $folder
     * @param  mixed $driverTypes
     * @return void
     */
    public function __construct($file = null, $config = null, $name = null, $folder = null, $driverTypes = null)
    {
        $this->name         = $name; 
        $this->folder       = $folder; 
        $this->file         = $file;
        $this->config       = $config;
        $this->driverTypes  = $driverTypes;
    }
    
    /**
     * Handle Request
     *
     * @return array
     * [fullPath, urlPath, path, tmp, name]
     */
    public function handle()
    {
        $migrate = $this->migrate();

        // Only try to save a copy of file if file does'nt exists
        if(!Tame::exists($migrate['fullPath'])){
            @move_uploaded_file($migrate['tmp'], $migrate['fullPath']);
        }

        // check if driver is local
        if(!$this->isLocalDriver()){
            // driver
            $driver = $this->config['driver'];

            // validate if package is installed
            $this->isDriverPackageInstalled(
                $this->getCloudAssociateClass($driver)
            );

            // initialize third-party bucket
            $bucket = new $this->driverTypes[$driver]();

            // get filename without extension
            $fileName = pathinfo($migrate['name'], PATHINFO_FILENAME);

            // save to cloud
            $save = $bucket->save($migrate['fullPath'], $fileName);
            
            // replace the migrate data
            $migrate['urlPath']     = $save;
            $migrate['path']        = $fileName;
        }
        
        return $migrate;
    }

    /**
     * Migrate Data
     *
     * @return array
     * [fullPath, urlPath, path, tmp, name]
     */
    private function migrate()
    {
        // get config value
        $config = $this->config;

        // get structure
        $structure = $this->getStructureType($config['structure']);

        // new generated file name
        $generateName = $this->file->generate($config['generate']);

        // Create parent storage folders
        $this->createParentFolder($this->folder);

        // Create structure folder
        $this->createStructureFolder($structure, $this->folder);

        // Create full path
        $filePath = $this->getFolderStorage($structure, $this->folder, $generateName);

        return array_merge($filePath, [
            'tmp'    => $this->file->tmp(),
            'name'   => $generateName,
            'driver' => $this->config['driver'],
        ]);
    }

    
    /**
     * Get Class Associated with drivers, for selected Cloud storage
     *
     * @param  string|null $mode
     * @return void
     */
    private function getCloudAssociateClass($mode = null)
    {
        return [
            's3' => [
                'exists'    => class_exists('Aws\S3\S3Client'),
                'message'   => "Class Aws\S3\S3Client not found: \nRequire the package by running: `composer require aws/aws-sdk-php`\n",
            ],
        ][$mode] ?? [
            'exists'    => false,
            'message'   => "Unkown Driver Bucket Selected",
        ];
    }
    
    /**
     * Check If Driver Package has been installed
     *
     * @param  mixed $class
     * @return void
     */
    static private function isDriverPackageInstalled($data = '')
    {
        try {
            if (!$data['exists']) {
                throw new CustomException(
                    $data['message'] . (new Exception)->getTraceAsString()
                );
            } 
        } catch (CustomException $e) {
            // Handle the exception silently (turn off error reporting)
            error_reporting(0);

            Manager::setHeaders(404, function() use($e){

                // create error logger
                Env::bootLogger();

                // Trigger a custom error
                trigger_error($e->getMessage(), E_USER_ERROR);
            });
        }
    }

}