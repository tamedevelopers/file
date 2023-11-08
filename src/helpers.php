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
     * - [401|402|403|404|405|405x|200|kb|mb|gb|and|width|height|files|file]
     * 
     * @param  array $config
     * - [limit|size|mime|baseDir|driver|structure|generate]
     * 
     * @param  array $class
     * - [error|success]
     *  
     * @return void
     */
    function config_file($message = [], $config = [], $class = [])
    {
        (new File)->globalConfig($message, $config, $class);
    }
}