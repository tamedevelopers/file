<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Traits;

use Tamedevelopers\Support\Tame;
use Tamedevelopers\Support\Server;
use Tamedevelopers\Support\Capsule\Forge;

/**
 * @property $config
 */
trait FileTrait{

    /**
     * Global Configuration
     *
     * @param  array $message
     * @param  array $config
     * @param  array $class
     * @return void
     */
    public function globalConfig($message = [], $config = [], $class = [])
    {
        // define constant to hold global error handler
        if(!defined('TAME_FILE_CONFIG')){

            // create message
            $message = $message + [
                '401'   => 'Select file to upload',
                '402'   => 'File upload is greater than allowed size of:',
                '403'   => 'Maximum file upload exceeded. Limit is:',
                '404'   => 'Uploaded file format not allowed! allowed format is:',
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

            // base dirrectory name
            $baseDirName = $config['baseDir'] ?? 'public';
            $baseDirName = trim((string) $baseDirName, '\/');

            // create config
            $config = array_merge([
                'limit'         => 1,
                'size'          => '2mb', 
                'mime'          => 'image',
                'baseDir'       => $baseDirName,
                'driver'        => 'local',
                'structure'     => 'default',
                'generate'      => true,
            ], $config);

            // create class
            $class = array_merge([
                'error'   => 'alert alert-danger',
                'success' => 'alert alert-success',
            ], $class);

            // Convert size to Bytes
            $config['size'] = Tame::sizeToBytes(
                !empty($config['size']) && (int) $config['size'] >= 1024 
                    ? Tame::byteToUnit($config['size']) 
                    : $config['size'] ?? '2mb'
            );

            // base domain path
            $config['baseUrl'] = Server::cleanServerPath(
                domain($config['baseDir'])
            );

            // convert base to absolute path
            $config['baseDir'] = Server::cleanServerPath(
                base_path($config['baseDir'])
            );

            // check for valid driver type
            // only change the default driver if found
            if(in_array($config['driver'], array_keys($this->driverTypes)))
            {
                $config['driver'] = $config['driver'];
            }

            // create default data
            $default = [
                'message' => $message,
                'config'  => $config,
                'class'   => $class,
            ];

            // define global config
            define('TAME_FILE_CONFIG', $default);
        }
    }

    /**
     * Remove unwanted Filter response status code
     *
     * @param  array $filter
     * Possible status code: [401, 402, 403, 404, 405, 200]
     * 
     * 401 => Select file to upload",
     * 402 => File upload is greater than allowed size of",
     * 403 => Maximum file upload exceeded. Limit is:",
     * 404 => Uploaded file format not allowed! allowed format is:",
     * 405 => Image size allowed error"
     * 200 => File uploaded successfully"
     * 
     * @return $this
     */
    public function filter(...$filter)
    {
        // flattern all into one array element
        $filter = Forge::flattenValue($filter);

        if(is_array($filter) && count($filter) > 0){

            foreach($filter as $value){
                // convert to int values
                $value = (int) $value;

                // if in error array keys
                if( in_array($value, array_keys($this->error)) && $value !== 200 ){
                    unset($this->error[$value]);
                }
            }
        }

        return $this;
    }

    /**
     * Filter Extension Type
     *
     * @param  mixed ...$extension
     * @return $this
     */
    public function filterExtention(...$extension)
    {
        // flattern all into one array element
        $extension = Forge::flattenValue($extension);
        
        $this->config['filter'] = $extension;

        return $this;
    }

    /**
     * Uploaded Image Size
     *
     * @param mixed $name
     * 
     * @return array
     * [width, height]
     */
    public function imageSize($name = null)
    {
        $instance = $this->name($name);

        // loop through to get temporary uploaded file data
        if(!empty($instance->files[$name]) && is_array($instance->files[$name])){
            foreach($instance->files[$name] as $item){
                return $item->imageSize();
            }
        }

        return [
            'width'  => null, 
            'height' => null
        ];
    }
    
    /**
     * Get Image Attributes
     *
     * @param string|null $sourcePath
     * 
     * @return array
     * [width, height]
     */
    public function getImageSize($sourcePath = null)
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
     * @param string|null $sourcePath
     * 
     * @return bool|string
     * - false on error
     */
    public function getMimeType($sourcePath = null)
    {
        return @mime_content_type($sourcePath);
    }

}