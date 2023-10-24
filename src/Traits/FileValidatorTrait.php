<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Traits;


use Tamedevelopers\Support\Tame;
use Tamedevelopers\File\FileStorage;


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
     * @return $this
     */
    private function proceedToSave()
    {
        // file data
        $fileItems = $this->fileItemsData();

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
                    $this->driverTypes
                );

                // get data response
                $this->uploads[] = $storage->handle();
            }
        }

        return $this;
    }

    /**
     * Proceed with validation
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
            /**
            * Select file to upload. - error 401
            */
            if(self::fileIsset($this->name) && isset($this->error['401'])){
                $this->data = [
                    'message'   => $this->translation(401),
                    'status'    => 401,
                    'files'     => null,
                ];
                $errors[] = true;
            }
        } 
        
        // begin file validation
        else{

            $validationAttempt = true;
            foreach($this->fileItemsData() as $key => $file){

                // Mime types
                $mimeTypes = $this->allowedMimeType()['mime'][$this->config['mime']]  ?? $this->allowedMimeType()['mime']['image'];

                // mimeExtensions
                $mimeExtensions = $this->allowedMimeType()['extension'][$this->config['mime']]  ?? $this->allowedMimeType()['extension']['image'];

                // if image width and height is allowed
                $imageSizeAllowed = $this->isImageWithHeightAllowed($file->imageSize());

                /**
                * File upload is greater than allowed size of. - error 402
                */
                if($file->size() > $this->config['size'])
                {
                    if(isset($this->error['402'])){
                        // convert size to btyes
                        $byteToUnit = Tame::byteToUnit(
                            bytes: $this->config['size'],
                            kb: $this->translation('kb'),
                            mb: $this->translation('mb'),
                            gb: $this->translation('gb'),
                        );
    
                        $this->data = [
                            "status"    => 402, 
                            "message"   => sprintf("%s %s", $this->translation('402'), $byteToUnit), 
                            "files"     => null,
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
                    if(isset($this->error['403'])){
                        $this->data = [
                            "status"    => 403, 
                            "message"   => sprintf("%s %s(%s)", 
                                $this->translation('403'), 
                                $this->config['limit'], 
                                $this->config['limit'] > 1 ? $this->translation('files') : $this->translation('file')
                            ), 
                            "files"     => null,
                        ];
                        $errors[] = true;
                    }
                    break;
                }

                /**
                * Uploaded file format not allowed. Allowed formats: - error 404
                */
                elseif(!in_array($file->mime(), $mimeTypes)){
                    if(isset($this->error['404'])){
                        $this->data = [
                            "status"    => 404, 
                            "message"   => sprintf("%s <br> %s %s", $file->name(), $this->translation('404'), implode(' ', $mimeExtensions)), 
                            "file"      => null,
                        ];
                        $errors[] = true;
                    }
                    break;
                }
                
                /**
                * Image size error. Image dimension allowed is: - error 405
                */
                elseif(!$imageSizeAllowed['response']){
                    if(isset($this->error['405'])){
                        $this->data = [
                            "status"    => 405,
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
                    "message"   => $this->translation('200'),
                ];
                $this->success = true;
            }
        }

        return $this;
    }
     
    /**
     * Run a callback 
     *
     * @param  Closure $function
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
        return defined('TAME_FILE_CONFIG') 
                ? TAME_FILE_CONFIG['message'][$mode] ?? TAME_FILE_CONFIG['message'] 
                : null;
    }
    
    /**
     * Call config method if not called
     *
     * @return void
     */
    private function callConfigIfNotCalled()
    {
        // define constant to hold global error handler
        if(!defined('TAME_FILE_CONFIG')){
            $this->globalConfig();

            // merge with default data
            $this->config = $this->dataMerge();
        } else{
            // merge with default data
            $this->config = $this->dataMerge();

            // class
            $this->class = TAME_FILE_CONFIG['class'];
        }
    }
    
    /**
     * Merge Data only when it's value is not empty
     *
     * @return array
     */
    private function dataMerge()
    {
        // default data from `TAME_FILE_CONFIG`
        $config = array_merge($this->config, TAME_FILE_CONFIG['config']);

        // Merge config only when custom setting is not empty
        foreach ($this->config as $key => $value) {
            if (!empty($value)) {
                $config[$key] = $value;
            }
        }

        return $config;
    }
    
    /**
     * Count items Total
     *
     * @return int
     */
    private function fileItemsDataCount()
    {
        return count($this->fileItemsData());
    }
    
    /**
     * Return file `items` data to be validated
     *
     * @return mixed
     */
    private function fileItemsData()
    {
        return $this->files[$this->name];
    }

    /**
     * Check if image width or height is allowed
     *
     * @param  mixed $value
     * @return bool
     */
    private function isImageWidthHeightAllowed($value = null)
    {
        if(!empty($value) && $value['size'] > 0){
            return true;
        }

        return false;
    }
    
    /**
     * Check if image width and height is allowed
     *
     * @param  mixed $size
     * @return array
     */
    private function isImageWithHeightAllowed($size = [])
    {
        // Check if config width and height is not empty
        $config = $this->config;
        if( $this->isImageWidthHeightAllowed($config['width']) && $this->isImageWidthHeightAllowed($config['height']) ){
            
            // only pass through if both witdh and height is more than 0
            // this means we have an actual image data
            if($size['width'] > 0 && $size['height'] > 0){

                // get setting data
                $sWidth = $config['width'];
                $sHeight = $config['height'];

                // if width and height is actual size check
                if($sWidth['actual'] && $sHeight['actual'])
                {
                    // if both width and height not same as allowed dimension
                    if($size['width'] != $sWidth['size'] && $size['height'] != $sHeight['size']){
                        return [
                            'response'  => false,
                            'message'   => sprintf(
                                "%s %s:%spx %s %s:%spx", 
                                $this->translation('405'), 
                                $this->translation('width'), 
                                $sWidth['size'],
                                $this->translation('and'),
                                $this->translation('height'),
                                $sHeight['size'],
                            )
                        ];
                    }
                } 
                
                // other case scenerio
                else{

                    // check if both are `false`
                    if(!$sWidth['actual'] && !$sHeight['actual']){

                        // if both width and height are less than allowed dimension
                        if($size['width'] < $sWidth['size'] || $size['height'] < $sHeight['size']){
                            return [
                                'response'  => false,
                                'message'   => sprintf(
                                    "%s %s:%spx %s %s:%spx", 
                                    $this->translation('405x'), 
                                    $this->translation('width'), 
                                    $sWidth['size'],
                                    $this->translation('and'),
                                    $this->translation('height'),
                                    $sHeight['size'],
                                )
                            ];
                        }
                    } 

                    // check for width validation
                    if($size['width'] < $sWidth['size']){
                        return [
                            'response'  => false,
                            'message'   => sprintf(
                                "%s %s:%spx", 
                                $this->translation('405x'), 
                                $this->translation('width'),
                                $sWidth['size'],
                            )
                        ];
                    }

                    // check for height validation
                    if($size['height'] < $sHeight['size']){
                        return [
                            'response'  => false,
                            'message'   => sprintf(
                                "%s %s:%spx", 
                                $this->translation('405x'), 
                                $this->translation('height'),
                                $sHeight['size'],
                            )
                        ];
                    }
                }
            }
        }

        return ['response' => true, 'message' => ''];
    }

}