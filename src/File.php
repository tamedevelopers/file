<?php

declare(strict_types=1);

namespace Tamedevelopers\File;

use Exception;
use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Tame;
use Tamedevelopers\File\ImageCompress;
use Tamedevelopers\File\ImageWatermark;
use Tamedevelopers\File\ImageAutoresize;
use Tamedevelopers\File\Traits\FileTrait;
use Tamedevelopers\File\Methods\FileMethod;
use Tamedevelopers\File\Traits\CommonTrait;
use Tamedevelopers\File\Traits\FileStorageTrait;
use Tamedevelopers\File\Traits\FilePropertyTrait;
use Tamedevelopers\File\Traits\FileValidatorTrait;


class File extends FileMethod{

    use FilePropertyTrait, 
        FileStorageTrait,
        FileValidatorTrait,
        CommonTrait,
        FileTrait;

    
    /**
     * __construct
     *
     * @param  string|null $name
     * @param  mixed $files
     * @return void
     */
    public function __construct($name = null, $files = null)
    {
        if(!empty($name)){
            $this->name = $name;
        }

        if(!empty($files)){
            $this->files = $files;
        }
    }
    
    /**
     * Initiliaze File Upload Using `name`
     *
     * @param  string $name
     * @return $this
     */
    static public function name($name)
    {
        return new static(
            $name,
            self::convertToFileHelper()
        );
    }
    
    /**
     * Begin Upload Validation
     *
     * @param  callable $function
     * @return $this
     */
    public function validate(callable $function = null)
    {
        if(!$this->success){
            $this->proceedToValidate()
                ->callback($function);
        }

        return $this;
    }
    
    /**
     * Begin Upload Validation
     *
     * @param  callable $function
     * @return $this
     */
    public function save(callable $function = null)
    {
        if($this->success){
            if(empty($this->folder)){
                $this->folder = 'public/images';
            }

            $this->proceedToSave()
                    ->callback($function);
        }

        return $this;
    }
    
    /**
     * Begin Image watermark
     *
     * @param string $watermarkSource The path to the watermark image.
     * 
     * @param string $position The position of the watermark. - Default is 'bottom-right'
     * - ['center', 'bottom-right', 'bottom-left', 'top-right', 'top-left']
     * 
     * @param  int $padding Padding in pixels (applied to all positions except 'center')
     * 
     * @return $this
     */
    public function watermark($watermarkSource, $position = 'bottom-right', $padding = 10)
    {
        $watermark = new ImageWatermark(
            object: $this, 
            name:   $this->name, 
            files:  $this->files,
        );

        $watermark->watermark(
            watermarkSource: $watermarkSource,
            position: $position,
            padding: $padding,
        );

        return $this;
    }
    
    /**
     * Resize an image based on width and/or height.
     *
     * @param int|null $width The desired width of the resized image (in pixels).
     * @param int|null $height The desired height of the resized image (in pixels).
     * 
     * @return $this
     */
    public function resize($width = null, $height = null)
    {
        $resize = new ImageAutoresize(
            object: $this, 
            name:   $this->name, 
            files:  $this->files,
        );

        $resize->resize($width, $height);

        return $this;
    }
    
    /**
     * Resize an image based on width and/or height.
     *
     * @param int|null $width The desired width of the resized image (in pixels).
     * @param int|null $height The desired height of the resized image (in pixels).
     * 
     * @return $this
     */

    /**
     * Compress an image file.
     * @return $this
     */
    public function compress()
    {
        $compress = new ImageCompress(
            object: $this, 
            name:   $this->name, 
            files:  $this->files,
        );

        $compress->compress();

        return $this;
    }
    
    /**
     * Change driver type
     *
     * @param string|null $driver
     * @return $this
     */
    public function driver($driver = null)
    {
        // trim string
        $driver = Str::lower($driver);

        // only change the default driver if found
        if(in_array($driver, array_keys($this->driverTypes))){
            $this->config['driver'] = $driver;
        }

        return $this;
    }
    
    /**
     * Set Destination Folder Directory
     *
     * @param string $folder
     * @return $this
     */
    public function folder($folder)
    {
        $this->folder = trim($folder);

        return $this;
    }
    
    /**
     * Set Folder Structure Creation
     *
     * @param string $structure
     * @return $this
     */
    public function structure($structure)
    {
        $this->config['structure'] = trim($structure);

        return $this;
    }
     
    /**
     * Upload size in mb
     *
     * @param  int|string|null $size
     * [optional] Default is (2mb)
     * 
     * 
     * @return $this
     */
    public function size($size = null)
    {
        $this->config['size'] = Tame::sizeToBytes(
            !empty($size) && (int) $size >= 1024
                ? Tame::byteToUnit($size)
                : $size ?? '2mb'
        );

        return $this;
    }
     
    /**
     * Upload limit
     *
     * @param  int|string|null $limit
     * @return $this
     */
    public function limit($limit = null)
    {
        $this->config['limit'] = self::numbericToInt($limit) ?: 1;

        return $this;
    }
    
    /**
     * Set width
     *
     * @param  int|string|null $width
     * @param  bool $width - Default is `true`
     * [optional] Set to false will make size equal to or greather than
     * 
     * @return $this
     */
    public function width($width = null, ?bool $actual = true)
    {
        $this->config['width'] = [
            'size' => self::numbericToInt($width),
            'actual' => $actual
        ];

        return $this;
    }
    
    /**
     * Set Height
     *
     * @param  int|string|null $height
     * @param  bool $width - Default is `true`
     * [optional] Set to false will make size equal to or greather than
     * 
     * @return $this
     */
    public function height($height = null, ?bool $actual = true)
    {
        $this->config['height'] = [
            'size' => self::numbericToInt($height),
            'actual' => $actual
        ];

        return $this;
    }
    
    /**
     * Set Mime Type
     *
     * @param  string|null $mime
     * @return $this
     */
    public function mime($mime = null)
    {
        $this->config['mime'] = $mime;

        return $this;
    }
    
    /**
     * Get Error Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['message'];
    }
    
    /**
     * Get Status Code
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->data['status'];
    }
    
    /**
     * Check if upload has an error
     *
     * @return int|null
     */
    public function hasError()
    {
        $status = $this->getStatus();
        if(!empty($status) && !$status == 0)
        {
            return true;
        }

        return false;
    }

    /**
     * Get First Element of Uploads
     * 
     * @param string|null $mode
     * 
     * @return array|string|null
     */
    public function first($mode = null)
    {
        $data = self::getUploads($this->uploads, true);

        return $data[$mode] ?? $data;
    }

    /**
     * Get all Element of Uploads
     * 
     * @return array|null
     */
    public function get()
    {
        return self::getUploads($this->uploads);
    }

    /**
     * Destructor for Removing Cloud Save Files
     * We automatically store a copy of uploaded files to local server
     * On success, We would want to remove that copy from the server
     * 
     * @return void
     */
    public function __destruct() 
    {
        // for cloud storage alone
        $this->destroyLocalCloudData();
    }

}