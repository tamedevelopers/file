<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Traits;

use Tamedevelopers\Support\Tame;

/**
 * @property $config
 */
trait FileTrait{
    
    /**
     * Set Base Domain Path
     *
     * @param  string|null $path
     * @return $this
     */
    private function setDomain($domain = null)
    {
        $this->config['baseUrl'] = domain($domain);

        return $this;
    }
    
    /**
     * Set Base Directory Path
     *
     * @param  string|null $path
     * @return $this
     */
    private function setDirectory($path = null)
    {
        $this->config['baseDir'] = base_path($path);

        return $this;
    }

    /**
     * Global Configuration
     *
     * @param  array $message
     * @param  array $config
     * @param  array $filterError
     * @return void
     */
    public function globalConfig($message = [], $config = [], $filterError = [])
    {
        // create message
        $message = $message + [
            '401'   => 'Select file to upload',
            '402'   => 'File upload is greater than allowed size of:',
            '403'   => 'Maximum file upload exceeded. Limit is:',
            '404'   => 'Uploaded file format not allowed. Allowed formats:',
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
        ];

        // create config
        $config = array_merge([
            'limit'         => 1,
            'size'          => 2097152, // 2mb
            'mime'          => 'images', // video|audio|files|images|general_image|general_media|general_file
            'baseUrl'       => domain(),
            'baseDir'       => base_path(),
            'driver'        => 'local', // local|s3
            'structure'     => 'default', // default|year|month|day
            'generate'      => true, // will always generate a unique() name for each uploaded file
        ], $config);


        // Convert size to Bytes
        $config['size'] = Tame::sizeToBytes(
            !empty($config['size']) && (int) $config['size'] >= 1024 
                ? Tame::byteToUnit($config['size']) 
                : $config['size'] ?? '2mb'
        );

        // create filterError
        $filterError = array_merge([], $filterError);

        // trim any leading '\/' and manually add by ourselves
        // this enable to make sure, paths are with a leading '/'
        $config['baseDir'] = trim((string) $config['baseDir'], '\/') . '/';
        $config['baseUrl'] = trim((string) $config['baseUrl'], '\/') . '/';

        // check for valid driver type
        // only change the default driver if found
        if(in_array($config['driver'], array_keys($this->driverTypes))){
            $config['driver'] = $config['driver'];
        }

        // create default data
        $default = [
            'message'       => $message,
            'config'        => $config,
            'filterError'   => $filterError,
        ];

        // if filter error is defined 
        if(!empty($default['filterError'])){

            // filter error to be remove from parent $this->error
            $this->filterError($default['filterError']);
        }

        // define constant to hold global error handler
        if(!defined('TAME_FILE_ERROR')){
            define('TAME_FILE_ERROR', $default);
        }
    }

    /**
     * Filter error to remove unwanted error response
     *
     * @param  array $errorDisallowed
     * 401 => ERROR_401 - Select file to upload",
     * 402 => ERROR_402 - File upload is greater than allowed size of",
     * 403 => ERROR_403 - Maximum file upload exceeded. Limit is:",
     * 404 => ERROR_404 - Uploaded file format not allowed. Allowed format is:",
     * 405 => ERROR_405 - Image size allowed error"
     * 200 => ERROR_200 - File uploaded successfully"
     * 
     * @return $this
     */
    public function filterError($errorDisallowed = [])
    {
        if(is_array($errorDisallowed) && count($errorDisallowed) > 0){

            foreach($errorDisallowed as $value){
                // convert to int values
                $value = (int) $value;

                // if in error array keys
                if(in_array($value, array_keys($this->error) )){
                    unset($this->error[$value]);
                }
            }
        }

        return $this;
    }
    
    /**
     * Get Image Attributes
     *
     * @param  string|null $sourcePath
     * 
     * @return array
     * [width, height]
     */
    public function getImageSize(string $sourcePath = null)
    {
        // get image attributes
        $imagePath = @getimagesize($sourcePath);

        return [
            'width'  => $imagePath[0] ?? null, 
            'height' => $imagePath[1] ?? null
        ];
    }

    /**
     * Get Mime Type
     *
     * @param  string|null $sourcePath
     * 
     * @return bool|string
     * - false on error
     */
    public function getMimeType(string $sourcePath = null)
    {
        return @mime_content_type($sourcePath);
    }

}