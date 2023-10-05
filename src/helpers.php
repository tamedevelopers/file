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


if (! function_exists('FileConfig')) {
    /**
     * Global Configuration
     *
     * @param  array $message
     * @param  array $config
     * 
     * @return void
     */
    function FileConfig($message = [], $config = [])
    {
        (new File)->globalConfig($message, $config);
    }
}