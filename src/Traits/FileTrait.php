<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Traits;

use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Tame;
use Tamedevelopers\Support\Server;
use Tamedevelopers\Support\Capsule\Forge;

/**
 * @property mixed $cachedConfig
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
        if(empty(static::$cachedConfig)){

            // create message
            $message = $message + [
                '401'               => 'Select file to upload',
                '402'               => 'File upload is greater than allowed size of:',
                '403'               => 'Maximum file upload exceeded. Limit is:',
                '404'               => 'Uploaded file format not allowed! allowed format is:',
                '405'               => 'Image dimension allowed is:',
                '405x'              => 'Image dimension should be greater than or equal to:',
                '200'               => 'File uploaded successfully:',
                'limit'             => 'Limit:',
                'max'               => 'Max:',
                'allowed_files'     => 'Allowed Files:',
                'kb'                => 'kb',
                'mb'                => 'mb',
                'gb'                => 'gb',
                'and'               => 'and',
                'width'             => 'width',
                'height'            => 'height',
                'files'             => 'files',
                'file'              => 'file',
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
            if(in_array($config['driver'], array_keys($this->drivers)))
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
            static::$cachedConfig = $default;
        }
    }

    /**
     * Remove unwanted Filter response status code
     *
     * @param  401|402|403|404|405|200|array $filter
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
     * Filter Extension Type (remove unwanted extensions) Extension you do not want to allow
     *
     * @param  string|array ...$extension       By raw extension keys (e.g. png, pdf)
     * @return $this
     */
    public function filterExtention(...$extension)
    {
        // flattern all into one array element
        $extension = Forge::flattenValue($extension);

        // clean any leading (dot) and convert to lowercase
        $extension = array_map(function ($value) {
            return Str::lower(ltrim($value, '.')); 
        }, $extension);
        
        $this->config['filter'] = $extension;

        return $this;
    }

    /**
     * Add Extension Type (add extra extensions) Extension you want to allow
     *
     * @param  string|array ...$extension      By raw extension keys (e.g. png, pdf)
     * @return $this
     */
    public function addExtention(...$extension)
    {
        // flattern all into one array element
        $extension = Forge::flattenValue($extension);

        // clean any leading (dot) and convert to lowercase
        $extension = array_map(function ($value) {
            return Str::lower(ltrim($value, '.')); 
        }, $extension);
        
        $this->config['extension'] = $extension;

        return $this;
    }

    /**
     * Uploaded Image Size
     *
     * @param mixed $name
     * @return array{width: mixed, height: mixed}
     */
    public function imageSize($name = null)
    {
        $instance = $this->name($name);

        // loop through to get temporary uploaded file data
        if(!empty($instance->file[$name]) && is_array($instance->file[$name])){
            foreach($instance->file[$name] as $item){
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
     * @return array{width: mixed, height: mixed}
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
     * @return bool|string
     */
    public function getMimeType($sourcePath = null)
    {
        return @mime_content_type($sourcePath);
    }

}