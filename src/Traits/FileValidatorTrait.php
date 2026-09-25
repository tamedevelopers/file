<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Traits;


use Closure;
use Tamedevelopers\File\FileStorage;
use Tamedevelopers\Support\Tame;


/**
 * @property mixed $cachedConfig
 * @method mixed fileIsset(mixed $name)
 * @method mixed isArray(mixed $name)
 * @method mixed isEmpty(mixed $name)
 */
trait FileValidatorTrait{
    
    /**
     * Destroy Uploaded Data that are for Cloud
     * - using the __desrtruct method
     *
     * @return void
     */
    private function destroyLocalCloudData()
    {
        if(self::isArray($this->uploads) && !$this->isLocalDriver()){
            foreach($this->uploads as $upload){
                @unlink($upload['fullPath']);
            }
        }
    }

    /**
     * Proceed with validation
     * 
     * @return $this
     */
    private function proceedToSave()
    {
        // file data
        $fileItems = $this->fileItemsData();

        // change the correct uploaded message
        $this->data["message"] = $this->translation('200');

        // check if file item is true 
        // and count is more than 0
        if(is_array($fileItems) && count($fileItems) > 0){

            // loop through each fileHelper element
            foreach($fileItems as $file){

                // instance of File Storage
                $storage = new FileStorage(
                    $file, 
                    $this->config, 
                    $this->name, 
                    $this->folder, 
                    $this->drivers
                );

                // get data response
                $this->uploads[] = $storage->handle();
            }
        }

        return $this;
    }

    /**
     * Proceed with validation
     * 
     * @return $this
     */
    private function proceedToValidate()
    {
        // error tracking
        $errors = [];
        $validationAttempt = false;
        $this->isValidatedCalled = true;

        // initilize configuration if not called
        $this->callConfigIfNotCalled();

        /**
        * When form has not been submitted, request will be empty
        */
        if(self::isEmpty($this->name))
        {
            $statusCode = '401';

            // Select file to upload. - error 401
            if(self::fileIsset($this->name) && isset($this->error[$statusCode])){
                $this->data = [
                    'message'   => $this->translation($statusCode),
                    'status'    => (int) $statusCode,
                    'files'     => null,
                ];
                $errors[] = true;
            }
        } 
        
        // begin file validation
        else {

            $validationAttempt = true;

            // get mime types and extensions
            $mime = $this->mimeAndExtension(); 

            // Mime types
            $mimeTypes = $mime['mimeTypes'];

            // mimeExtensions
            $mimeExtensions = $mime['mimeExtensions'];

            foreach($this->fileItemsData() as $key => $file){

                // if image width and height is allowed
                $imageSizeAllowed = $this->validateImageDimensions($file->imageSize(), $file);

                // validate mime and extension
                $validateMime = in_array($file->mime(), $mimeTypes) 
                    && in_array($file->extensionWithDot(), $mimeExtensions);

                /**
                * File upload is greater than allowed size of. - error 402
                */
                if($file->size() > $this->config['size'])
                {
                    $statusCode = '402';
                    if(isset($this->error[$statusCode])){
                        // convert size to btyes
                        $byteToUnit = Tame::byteToUnit(
                            bytes: $this->config['size'],
                            format: true,
                            kb: $this->translation('kb'),
                            mb: $this->translation('mb'),
                            gb: $this->translation('gb'),
                        );
    
                        $this->data = [
                            "status"  => (int) $statusCode, 
                            "message" => sprintf("%s %s", 
                                $this->translation($statusCode), 
                                $byteToUnit
                            ), 
                            "files" => null,
                        ];
                        $errors[] = true;
                    }
                    break;
                }

                /**
                * Maximum file upload exceeded. - error 403
                */
                elseif($this->fileItemsDataCount() > $this->config['limit'])
                {
                    $statusCode = '403';
                    if(isset($this->error[$statusCode])){
                        $limit = $this->config['limit'];
                        $trans = $limit > 1 
                            ? $this->translation('files')
                            : $this->translation('file');

                        $this->data = [
                            "status"  => (int) $statusCode, 
                            "message" => sprintf("%s %s(%s)", 
                                $this->translation($statusCode), 
                                $limit, 
                                $trans
                            ), 
                            "files" => null,
                        ];
                        $errors[] = true;
                    }
                    break;
                }

                /**
                * Uploaded file format not allowed. Allowed formats: - error 404
                */
                elseif($validateMime === false){
                    $statusCode = '404';
                    if(isset($this->error[$statusCode])){
                        $this->data = [
                            "status"  => (int) $statusCode, 
                            "message" => sprintf("%s <br> %s %s", 
                                $file->name(), 
                                $this->translation($statusCode), 
                                implode(' ', $mimeExtensions)
                            ), 
                            "file" => null,
                        ];
                        $errors[] = true;
                    }
                    break;
                }
                
                /**
                * Image size error. Image dimension allowed is: - error 405
                */
                elseif(!$imageSizeAllowed['response']){
                    $statusCode = '405';
                    if(isset($this->error[$statusCode])){
                        $this->data = [
                            "status"    => (int) $statusCode,
                            "message"   => sprintf($imageSizeAllowed['message'], $file->name()), 
                            "file"      => null,
                        ];
                        $errors[] = true;
                    }
                    break;
                } 
            }
        }

        // if no error was found in the error array
        if(empty($errors)){
            if($validationAttempt){
                $this->data = [
                    "status"    => 200,
                    "message"   => "File validated successfully:",
                ];
                $this->success = true;
            }
        }

        return $this;
    }
     
    /**
     * Run a callback 
     *
     * @param  Closure $closure
     * @return mixed
     */
    private function callback($closure = null)
    {
        if(Tame::isClosure($closure)){
            $closure($this);
        }
    }
    
    /**
     * Get Transalation Message
     *
     * @param  mixed $mode
     * @return mixed
     */
    private function translation($mode = null)
    {
        $cached = static::$cachedConfig;

        return !empty($cached)
            ? $cached['message'][$mode] ?? $cached['message'] 
            : null;
    }
    
    /**
     * Call config method if not called
     */
    private function callConfigIfNotCalled(): void
    {
        // define constant to hold global error handler
        if(empty(static::$cachedConfig)){
            $this->globalConfig();

            // merge with default data
            $this->config = $this->dataMerge();
        } else{
            // merge with default data
            $this->config = $this->dataMerge();

            // class
            $this->class = static::$cachedConfig['class'];
        }
    }
    
    /**
     * Merge Data only when it's value is not empty
     */
    private function dataMerge(): array
    {
        $config = array_merge($this->config, static::$cachedConfig['config']);

        // Merge config only when custom setting is not empty
        foreach ($this->config as $key => $value) {
            if (!empty($value) || is_bool($value)) {
                $config[$key] = $value;
            }
        }

        return $config;
    }
    
    /**
     * Count items Total
     */
    private function fileItemsDataCount(): int
    {
        return count($this->fileItemsData());
    }
    
    /**
     * Return file `items` data to be validated
     */
    private function fileItemsData(): mixed
    {
        return $this->files[$this->name];
    }

    /**
     * Check if image width or height is allowed
     *
     * @param  mixed $value
     * @return bool
     */
    private function isImageSizeAllowed($value = null)
    {
        return is_array($value)
            && isset($value['size'])
            && (int) $value['size'] > 0;
    }

    /**
     * Normalize width/height config safely
     *
     * @param mixed $value
     * @param array $default
     * @return array
     */
    private function normalizeDimension($value, array $default): array
    {
        if ($this->isImageSizeAllowed($value)) {
            return [
                'size'   => (int) $value['size'],
                'actual' => (bool) ($value['actual'] ?? false),
            ];
        }

        return $default;
    }
    
    /**
     * Check if image width and height is allowed
     *
     * @param array{'width': int, 'height': int} $size 
     * @param mixed $file
     * @return array
     */
    private function validateImageDimensions($size = [], $file = null)
    {
        // Check if config width and height is not empty
        $config = $this->config;

        // Normalize dimension structure
        $defaultDimension = [
            'size'   => 0,
            'actual' => false,
        ];

        // if file is a valid image
        if($file->isImage()){
            $config['width'] = $this->normalizeDimension($config['width'], $defaultDimension);
            $config['height'] = $this->normalizeDimension($config['height'], $defaultDimension);

            $sWidth  = $config['width'];
            $sHeight = $config['height'];

            // Ensure valid image dimensions
            if (empty($size['width']) || empty($size['height']) || $size['width'] <= 0 || $size['height'] <= 0) {
                return [
                    'response' => false,
                    'message'  => 'Invalid image dimensions',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | WIDTH VALIDATION
            |--------------------------------------------------------------------------
            */
            if ($sWidth['size'] > 0) {

                // Exact match required
                if ($sWidth['actual']) {
                    if ($size['width'] !== (int) $sWidth['size']) {
                        return [
                            'response' => false,
                            'message'  => sprintf(
                                "%s %s:%spx",
                                $this->translation('405'),
                                $this->translation('width'),
                                $sWidth['size']
                            ),
                        ];
                    }
                }

                // Minimum required
                else {
                    if ($size['width'] < (int) $sWidth['size']) {
                        return [
                            'response' => false,
                            'message'  => sprintf(
                                "%s %s:%spx",
                                $this->translation('405x'),
                                $this->translation('width'),
                                $sWidth['size']
                            ),
                        ];
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | HEIGHT VALIDATION
            |--------------------------------------------------------------------------
            */
            if ($sHeight['size'] > 0) {

                // Exact match required
                if ($sHeight['actual']) {
                    if ($size['height'] !== (int) $sHeight['size']) {
                        return [
                            'response' => false,
                            'message'  => sprintf(
                                "%s %s:%spx",
                                $this->translation('405'),
                                $this->translation('height'),
                                $sHeight['size']
                            ),
                        ];
                    }
                }

                // Minimum required
                else {
                    if ($size['height'] < (int) $sHeight['size']) {
                        return [
                            'response' => false,
                            'message'  => sprintf(
                                "%s %s:%spx",
                                $this->translation('405x'),
                                $this->translation('height'),
                                $sHeight['size']
                            ),
                        ];
                    }
                }
            }
        }

        return ['response' => true, 'message' => ''];
    }

    /**
     * Get Mime Types and Extensions
     */
    private function mimeAndExtension(): array
    {
        $default = $this->allowedMimeType();

        // Get filters if any or set to empty array
        $filter = $this->config['filter'] ?? [];

        // Get extensions if any or set to empty array
        $extension = $this->config['extension'] ?? [];

        // allowed mime types after excluding filter types
        $filteredMime = $this->excludeTypes($filter); 
        $mime = $this->includeTypes($extension, $filteredMime); 

        // Mime types
        $mimeTypes = $mime['mime'][$this->config['mime']] ?? $default['mime']['image'];
        $mimeExtensions = $mime['extension'][$this->config['mime']]  ?? $default['extension']['image'];

        return [
            'mimeTypes'        => $mimeTypes,
            'mimeExtensions'   => $mimeExtensions,
        ];
    }

}