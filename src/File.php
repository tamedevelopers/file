<?php

declare(strict_types=1);

namespace Tamedevelopers\File;

use Closure;
use Tamedevelopers\File\ImageAutoresize;
use Tamedevelopers\File\ImageCompress;
use Tamedevelopers\File\ImageWatermark;
use Tamedevelopers\File\Methods\FileMethod;
use Tamedevelopers\File\Traits\CommonTrait;
use Tamedevelopers\File\Traits\FilePropertyTrait;
use Tamedevelopers\File\Traits\FileStorageTrait;
use Tamedevelopers\File\Traits\FileTrait;
use Tamedevelopers\File\Traits\FileValidatorTrait;
use Tamedevelopers\Support\Capsule\FileBag;
use Tamedevelopers\Support\Server;
use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Tame;


/**
 * File
 *
 * @package   tamedevelopers\file
 * @author    Tame Developers <tamedevelopers@gmail.com>
 * @copyright 2021-2026 Tame Developers
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
            $this->files = FileBag::collect($name);
        }
    }
    
    /**
     * Initiliaze File Upload Using `name`
     *
     * @param  string|null $fileName
     * @return $this
     */
    static public function name($fileName = null)
    {
        return new static($fileName);
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
            if(in_array($this->getStatus(), [401, 402, 403, 404, 405])){
                $this->callback($closure);
            }
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

            $this->proceedToSave()->callback($closure);
        }

        return $this;
    }
    
    /**
     * Begin Image watermark
     *
     * @param string $watermarkSource The path to the watermark image.
     * @param 'center'|'bottom-right'|'bottom-left'|'top-right'|'top-left' $position  Default is 'bottom-right'
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
     * @param int|null $width   The desired width of the resized image (in pixels).
     * @param int|null $height  The desired height of the resized image (in pixels).
     * @param bool $equalResize Force exact target dimensions (center-cropped cover) if true.
     * @return $this
     */
    public function resize($width = null, $height = null, $equalResize = false)
    {
        $resize = new ImageAutoresize(
            object: $this, 
            name:   $this->name, 
            files:  $this->files,
        );

        $resize->resize($width, $height, $equalResize);

        return $this;
    }

    /**
     * Compress an image file.
     * 
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
     * @param 'local'|'s3' $driver
     * @return $this
     */
    public function driver($driver)
    {
        // trim string
        $driver = Str::lower($driver);

        // only change the default driver if found
        if(in_array($driver, array_keys($this->drivers))){
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
     * @param 'default'|'year'|'month'|'day' $structure
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
     * @param  int|string $size  Default is (2mb)
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
        $this->config['limit'] = self::numericToInt($limit) ?: 1;

        return $this;
    }
     
    /**
     * Show limit text
     *
     * @param  bool $show
     * @return $this
     */
    public function limitText($show = true)
    {
        $this->config['limitText'] = $show;

        return $this;
    }
     
    /**
     * ALlias of limitText
     *
     * @param  bool $show
     * @return $this
     */
    public function showLimitText($show = true)
    {
        return $this->limitText($show);
    }
    
    /**
     * Set width
     *
     * @param  int|string $width
     * @param  bool $actual     False will make size equal to or greather than
     * @return $this
     */
    public function width($width, ?bool $actual = true)
    {
        $this->config['width'] = [
            'size' => self::numericToInt($width),
            'actual' => $actual
        ];

        return $this;
    }
    
    /**
     * Set Height
     *
     * @param  int|string $height
     * @param  bool $actual     False will make size equal to or greather than
     * @return $this
     */
    public function height($height, ?bool $actual = true)
    {
        $this->config['height'] = [
            'size' => self::numericToInt($height),
            'actual' => $actual
        ];

        return $this;
    }
    
    /**
     * Set Mime Type
     *
     * @param  'video'|'audio'|'file'|'image'|'zip'|'pdf'|'xls'|'doc'|'general_image'|'general_media'|'general_file' $mime
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
     * @param  string|null $message
     * @return string
     */
    public function getMessage($message = null)
    {
        $this->data['message'] = empty($message) ? $this->data['message'] : $message;

        return $this->data['message'];
    }

    /**
     * Get Error Validation Message
     */
    public function getValidationMessage(): string
    {
        // initilize configuration if not called
        $this->callConfigIfNotCalled();
        
        // get mime types and extensions
        $mime = $this->mimeAndExtension(); 

        // Mime types
        $mimeTypes = $mime['mimeTypes'];

        // mimeExtensions
        $mimeExtensions = $mime['mimeExtensions'];

        // convert size to btyes
        $byteToUnit = Tame::byteToUnit(
            bytes: $this->config['size'],
            format: true,
            kb: $this->translation('kb'),
            mb: $this->translation('mb'),
            gb: $this->translation('gb'),
        );

        // format limit
        $fileText = $this->config['limit'] > 1 
            ? $this->translation('files') 
            : $this->translation('file');

        $maxText = $this->translation('max');
        $limitText = $this->translation('limit');
        $allowedText = $this->translation('allowed_files'); 

        // formatted ectensions text
        $result = tcollect($mimeExtensions)
                    ->map(fn($ext) => strtoupper(ltrim($ext, '.')))
                    ->implode(', ');

        return sprintf("%s %s (%s) %s", 
            $allowedText, 
            $result, 
            "{$maxText} {$byteToUnit}",
            $this->config['limit'] > 1 && $this->config['limitText']
            ? "- ({$limitText} {$this->config['limit']} {$fileText})" 
            : ""
        );
    }
    
    /**
     * Get Status Code
     */
    public function getStatus(): int
    {
        return (int) $this->data['status'];
    }
    
    /**
     * Get Class Error
     */
    public function getClass(): string
    {
        if($this->hasError()){
            return $this->class['error'];
        }

        return $this->success ? $this->class['success'] : '';
    }
    
    /**
     * Check if upload has an error
     */
    public function hasError(): bool
    {
        $status = $this->getStatus();
        if(!empty($status) && !in_array($status, [0, 200])){
            return true;
        }

        return false;
    }
    
    /**
     * Check if file has been uploaded
     */
    public function isCompleted(): bool
    {
        return $this->getStatus() === 200;
    }

    /**
     * Get Uploader name
     */
    public function getName(): string|null
    {
        return $this->name;
    }

    /**
     * Get Uploaded folder path
     * 
     * @param string|null $path
     * @return string|null
     */
    public function getFolder($path = null)
    {
        if(empty($path)){
            return $this->folder;
        }

        if(is_array($path)){
            $path = Str::head($path);
        }
        
        return "{$this->folder}/{$path}";
    }

    /**
     * Get First Element of Uploads
     * 
     * @param 'name'|'path'|'url'|null $mode
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
     * @param 'name'|'path'|'url'|null $mode
     * @return array|null
     */
    public function get($mode = null)
    {
        $data = self::getUploads($this->uploads);
        if(is_null($mode)){
            return $data;
        }

        return array_column($data ?: [], $mode);
    }

    /**
     * Return validator form request
     * 
     * @return \Tamedevelopers\Validator\Validator
     */
    public function form()
    {
        return (new \Tamedevelopers\Validator\Validator)
            ->token(false)
            ->all();
    }

    /**
     * Unlink File from Server
     *
     * @param string $pathToFile    Full path to file is required
     * @param string|null $restrictedfileName   File to check against before unlinking
     * @return void
     */
    static public function unlink(string $pathToFile, $restrictedfileName = null)
    {
        Tame::unlink($pathToFile, $restrictedfileName);
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
     * Publish JavaScript code to automatically convert file inputs to support multiple files
     * Call this method and echo the output in your HTML head or before file inputs
     * 
     * @return string JavaScript code
     */
    public function publishJS()
    {
        return FileBag::publishJS();
    }
    
    /**
     * Get Error Data
     */
    public function getError(): array
    {
        return $this->error;
    }
    
    /**
     * Get Config Data
     */
    public function getConfig(): array
    {
        return $this->config;
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