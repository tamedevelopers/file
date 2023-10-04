<?php 

use Tamedevelopers\File\File;



if (! function_exists('TameFile')) {
    /**
     * Instance of File Class
     * 
     * @return \Tamedevelopers\File\File
     */
    function TameFile()
    {
        return new File();
    }
}


if (! TameIsLaravelDetect() && ! function_exists('FileConfig')) {
    /**
     * Global Configuration
     *
     * @param  array $message
     * @param  array $config
     * @param  array $filterError
     * 
     * @return void
     */
    function FileConfig($message = [], $config = [], $filterError = [])
    {
        // $File = new File();
        (new File)->globalConfig($message, $config, $filterError);
    }
}