<?php

namespace Tamedevelopers\File\Traits;


trait FileMagicTrait{
    
    /**
     * object
     *
     * @var mixed
     */
    private $object;

    /**
     * name
     *
     * @var string|null
     */
    private $name;
    
    /**
     * object
     *
     * @var mixed
     */
    private $files;
    
    /**
     * folder
     *
     * @var string|null
     */
    private $folder;
    
    /**
     * config
     *
     * @var array|null
     */
    private $config;
    
    /**
     * uploads data
     *
     * @var array|null
     */
    private $uploads;

    
    /**
     * __construct
     *
     * @param  mixed $object
     * @param  mixed $name
     * @param  mixed $files
     * @return void
     */
    public function __construct($object = null, $name = null, $files = null)
    {
        $this->object   = $object;
        $this->name     = $name;
        $this->files    = $files;
        $this->folder   = $object->folder ?? null;
        $this->config   = $object->config ?? null;
        $this->uploads  = $object->uploads ?? null;
    }
    
    /**
     * Try to Run Cloub Buckets
     * Only when driver is not local
     *
     * @param  mixed $upload
     * @return void
     */
    private function bucket($upload)
    {
        if(!$this->isLocalDriver()){
            // initialize third-party bucket
            $bucket = new $this->object->driverTypes[$this->config['driver']]();

            // get filename without extension
            $fileName = pathinfo($upload['name'], PATHINFO_FILENAME);

            // save to cloud
            $bucket->update($upload['fullPath'], $fileName);
        }
    }
        
    /**
     * loop handler
     *
     * @param  mixed $function
     * @return void
     */
    private function loop(callable $function)
    {
        if(is_array($this->uploads)){
            $function($this);
        }
    }

    /**
     * Load an image from a file path and return the image resource.
     *
     * @param  array $upload
     * @return array|false
     */
    private function getImageResource($upload)
    {
        return @getimagesize($upload['fullPath']);
    }

    /**
     * Load an image from a file path and return the image resource.
     *
     * @param  array $upload
     * @param  array $file
     * @return array|false
     */
    private function createGDImage($imageSource, $path)
    {
        // get mime
        $mime = $imageSource['mime'] ?? false;
        if(!$mime){
            return $mime;
        }

        // Load image based on MIME type
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }
    
    /**
     * Remove Temp File From Server 
     * - Only when driver is not `local`
     *
     * @return void
     */
    private function removeTempFile()
    {
        if(!$this->isLocalDriver()){
            @unlink($this->uploads['fullPath']);
        }
    }

}