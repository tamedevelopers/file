<?php

declare(strict_types=1);

namespace Tamedevelopers\File;

use Exception;
use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Tame;
use Tamedevelopers\Support\Server;
use Tamedevelopers\File\ImageCompress;
use Tamedevelopers\File\ImageWatermark;
use Tamedevelopers\Validator\Validator;
use Tamedevelopers\File\ImageAutoresize;
use Tamedevelopers\File\Traits\FileTrait;
use Tamedevelopers\File\Methods\FileMethod;
use Tamedevelopers\File\Traits\CommonTrait;
use Tamedevelopers\File\Traits\FileStorageTrait;
use Tamedevelopers\File\Traits\FilePropertyTrait;
use Tamedevelopers\File\Traits\FileValidatorTrait;


/**
 * File
 *
 * @package   tamedevelopers\file
 * @author    Tame Developers <tamedevelopers@gmail.com>
 * @author    Fredrick Peterson <fredi.peterson2000@gmail.com>
 * @copyright 2021-2023 Tame Developers
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/tamedevelopers/file
 */
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
     * @param  string|null $name
     * @return $this
     */
    static public function name($name = null)
    {
        return new static(
            $name,
            self::convertToFileHelper()
        );
    }

    /**
     * Begin Upload Validation
     *
     * @param  Closure|null $closure
     * @return $this
     */
    public function validate($closure = null)
    {
        $this->proceedToValidate();

        if(!$this->success){
            $this->callback($closure);
        }

        return $this;
    }
    
    /**
     * Begin Upload Confirmation
     *
     * @param  Closure|null $closure
     * @return $this
     */
    public function save($closure = null)
    {
        $this->ignoreIfValidatorHasBeenCalled();

        if($this->success){
            if(empty($this->folder)){
                $this->folder = '';
            }

            $this->proceedToSave()
                    ->callback($closure);
        }

        return $this;
    }
    
    /**
     * Begin Image watermark
     *
     * @param string $watermarkSource The path to the watermark image.
     * 
     * @param string $position The position of the watermark. - Default is 'bottom-right'
     * ['center', 'bottom-right', 'bottom-left', 'top-right', 'top-left']
     * 
     * @param  int $padding Padding in pixels (applied to all positions except 'center')
     * 
     * @return $this
     */
    public function watermark($watermarkSource, $position = 'bottom-right', $padding = 15)
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
     * Change base directory
     *
     * @param string $directory
     * @return $this
     */
    public function baseDir($directory)
    {
        // clean the path given
        $baseDirName = trim((string) $directory, '\/');

        // base domain path
        $this->config['baseUrl'] = Server::cleanServerPath(
            domain($baseDirName)
        );

        // trim string
        $this->config['baseDir'] = Server::cleanServerPath(
            base_path($baseDirName)
        );

        return $this;
    }
    
    /**
     * Allow generation of new name for uploaded files
     *
     * @param bool $allow
     * @return $this
     */
    public function generate(?bool $allow = true)
    {
        $this->config['generate'] = $allow;

        return $this;
    }
    
    /**
     * Change driver type
     *
     * @param string $driver
     * @return $this
     */
    public function driver($driver)
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
     * Set Structure Creation
     *
     * @param string $structure
     * - Keys [default, year, month, day]
     * 
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
     * @param  int|string $size
     * [optional] Default is (2mb)
     * 
     * @return $this
     */
    public function size($size)
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
     * @param  int|string $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->config['limit'] = self::numbericToInt($limit) ?: 1;

        return $this;
    }
    
    /**
     * Set width
     *
     * @param  int|string $width
     * @param  bool $width - Default is `true`
     * [optional] Set to false will make size equal to or greather than
     * 
     * @return $this
     */
    public function width($width, ?bool $actual = true)
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
     * @param  int|string $height
     * @param  bool $width - Default is `true`
     * [optional] Set to false will make size equal to or greather than
     * 
     * @return $this
     */
    public function height($height, ?bool $actual = true)
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
     * @param  string $mime
     * [available keys] 
     * - video|audio|file|image|general_image|general_media|general_file
     * 
     * @return $this
     */
    public function mime($mime)
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
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->data['status'];
    }
    
    /**
     * Get Class Error
     *
     * @return string
     */
    public function getClass()
    {
        if($this->hasError()){
            return $this->class['error'];
        }

        return $this->success ? $this->class['success'] : null;
    }

    /**
     * Get Uploader name
     * 
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Check if upload has an error
     *
     * @return bool
     */
    public function hasError()
    {
        $status = $this->getStatus();
        if(!empty($status) && !in_array($status, [0, 200]))
        {
            return true;
        }

        return false;
    }
    
    /**
     * Check if file has been uploaded
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->getStatus() === 200;
    }

    /**
     * Get First Element of Uploads
     * 
     * @param string|null $mode
     * - [name, path, url]
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
     * @param string|null $mode
     * - [name, path, url]
     * 
     * @return array|null
     */
    public function get($mode = null)
    {
        $data = self::getUploads($this->uploads);
        if(is_null($mode)){
            return $data;
        }

        return array_column($data, $mode);
    }

    /**
     * Return validator form request
     * 
     * @return Tamedevelopers\Validator\Validator
     */
    public function form()
    {
        return (new Validator)
                    ->token(false)
                    ->all();
    }

    /**
     * Unlink File from Server
     *
     * @param string $fileToUnlink
     * @param string|null $checkFile
     * [optional] File to check against before unlinking
     * 
     * @return void
     */
    static public function unlink(string $fileToUnlink, $checkFile = null)
    {
        Tame::unlinkFile(
            base_path($fileToUnlink), 
            $checkFile
        );
    }

    /**
     * Echo `json_encode` with response and message
     *
     * @param  int $response
     * @param  mixed $message
     * @return mixed
     */
    public function echoJson(int $response = 0, $message = null)
    {
        return Tame::echoJson($response, $message);
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