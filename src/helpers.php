<?php 

use Tamedevelopers\File\File; 



if (! function_exists('TameFile')) {
    /**
     * Instance of File Class
     * 
     * @param  string|null $name
     * @return \Tamedevelopers\File\File
     */
    function TameFile($name = null)
    {
        return (new File)->name($name);
    }
}


if (! function_exists('config_file')) {
    /**
     * Global Configuration
     *
     * @param  array{
     *  '401': string,
     *  '402': string,
     *  '403': string,
     *  '404': string,
     *  '405': string,
     *  '405x': string,
     *  '200': string,
     *  kb: string,
     *  mb: string,
     *  and: string,
     *  width: string,
     *  height: string,
     *  files: string,
     *  file: string,
     *  limit: string,
     *  allowed_files: string,
     *  max: string,
     * } $message
     * 
     * @param  array{
     *  limit: string|int,
     *  size: string|int,
     *  mime: string,
     *  baseDir: string,
     *  driver: 'local'|'s3',
     *  structure: 'default'|'year'|'month'|'day',
     *  generate: bool,
     * } $config
     * 
     * @param  array{error: mixed, success: mixed} $class
     * @return void
     */
    function config_file($message = [], $config = [], $class = [])
    {
        (new File)->globalConfig($message, $config, $class);
    }
}