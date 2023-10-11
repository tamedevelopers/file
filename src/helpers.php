<?php 

use Tamedevelopers\File\File;



if (! function_exists('TameFile')) {
    /**
     * Instance of File Class
     * @param  string|null $name
     * 
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
     * @param  array $message
     * @param  array $config
     * @param  array $class
     * 
     * @return void
     */
    function config_file($message = [], $config = [], $class = [])
    {
        (new File)->globalConfig($message, $config, $class);
    }
}