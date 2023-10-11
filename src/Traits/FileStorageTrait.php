<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Traits;

use Tamedevelopers\Support\Time;


/**
 * @property-read mixed $config
 * @property \Tamedevelopers\Support\Time|mixed
 */
trait FileStorageTrait {
    

    /**
     * Get each image upload folder storage data
     *
     * @param  mixed $structure
     * @param  mixed $uploadDirectory
     * @param  mixed $newGenFileName
     * 
     * @return array
     * 
     * [fullPath, urlPath, path]
     */
    protected function getFolderStorage($structure, $uploadDirectory, $newGenFileName)
    {
        // Creating our folder structure
        $folder = $this->createTimeBaseFolder($uploadDirectory);

        switch ($structure) 
        {
            case 'year':
                $fullPath   = "{$folder['year']}/{$newGenFileName}";
                $path = str_replace($this->config['baseDir'], '', $fullPath);
                break;
            case 'month':
                $fullPath   = "{$folder['month']}/{$newGenFileName}";
                $path = str_replace($this->config['baseDir'], '', $fullPath);
                break;
            case 'day':
                $fullPath   = "{$folder['day']}/{$newGenFileName}";
                $path = str_replace($this->config['baseDir'], '', $fullPath);
                break;
            default:
                $fullPath   = "{$this->config['baseDir']}{$uploadDirectory}/{$newGenFileName}";
                $path = str_replace($this->config['baseDir'], '', $fullPath);
                break;
        }

        return [
            'fullPath'  => $fullPath, 
            'urlPath'   => "{$this->config['baseUrl']}{$path}",
            'path'      => $path,
        ];
    }
    
    /**
     * Create Non Existable Base Folder
     *
     * @param  mixed $uploadDirectory
     * @return void
     */
    protected function createParentFolder($uploadDirectory)
    {
        // explode path using `/`
        // this way we're able tpo separate all [dir] and [subdir]
        $directorySegments = explode('/', $uploadDirectory);

        $segmentPath = "";

        // loop through each directory path
        foreach($directorySegments as $key => $segment){
            
            $separator = $key === 0 ? '' : '/';
            $segmentPath .= $separator . $segment;

            // create absolute path
            $fullPath = "{$this->config['baseDir']}{$segmentPath}";

            // Create folder if not exist
            if(!is_dir($fullPath))
            {
                @mkdir($fullPath, 0777);

                // Create index file
                $this->createDefaultRestrictedFiles($fullPath);
            }
        }
    }
     
    /**
     * Creating storage structure folder
     *
     * @param  mixed $structure
     * @param  mixed $uploadDirectory
     * @return void
     */
    protected function createStructureFolder($structure, $uploadDirectory)
    {
        // Creating our folder structure
        $folder = $this->createTimeBaseFolder($uploadDirectory);

        switch ($structure) { 
            case 'year':
                if (!is_dir($folder['year'])) {
                    @mkdir($folder['year'], 0777);
                    $this->createDefaultRestrictedFiles($folder['year']);
                }
                break;
            case 'month':
                if (!is_dir($folder['year'])) {
                    @mkdir($folder['year'], 0777);
                    $this->createDefaultRestrictedFiles($folder['year']);
                }
                if (!is_dir($folder['month'])) {
                    @mkdir($folder['month'], 0777);
                    $this->createDefaultRestrictedFiles($folder['month']);
                }
                break;
            case 'day':
                if (!is_dir($folder['year'])) {
                    @mkdir($folder['year'], 0777);
                    $this->createDefaultRestrictedFiles($folder['year']);
                }
                if (!is_dir($folder['month'])) {
                    @mkdir($folder['month'], 0777);
                    $this->createDefaultRestrictedFiles($folder['month']);
                }
                if (!is_dir($folder['day'])) {
                    @mkdir($folder['day'], 0777);
                    $this->createDefaultRestrictedFiles($folder['day']);
                }
                break;
        }
    }
    
    /**
     * Creating folder time base structure
     *
     * @param  mixed $uploadDirectory
     * @return array
     */
    protected function createTimeBaseFolder($uploadDirectory = null)
    {
        $now = strtotime("now");

        $time = [
            "year"  => Time::timestamp($now, 'Y'),
            "month" => Time::timestamp($now, 'n'),
            "day"   => Time::timestamp($now, 'j'),
            "now"   => $now
        ];

        return [
            'year'  => "{$this->config['baseDir']}{$uploadDirectory}/{$time['year']}",
            'month' => "{$this->config['baseDir']}{$uploadDirectory}/{$time['year']}/{$time['month']}",
            'day'   => "{$this->config['baseDir']}{$uploadDirectory}/{$time['year']}/{$time['month']}/{$time['day']}", 
            'now'   => $time['now']
        ];
    }
     
    /**
     * Create default restricted files
     *
     * @param  mixed $path
     * @return void
     */
    protected function createDefaultRestrictedFiles($path)
    {
        //Create index file
        if (!file_exists("{$path}/index.html") ) {
            @$fsource = fopen("{$path}/index.html", 'w+');
            if(is_resource($fsource)){
                fwrite($fsource, "Restricted Access");
                fclose($fsource);
            }
        }

        //Create apache file -- .htaccess
        if (!file_exists("{$path}/.htaccess") ) {
            @$fsource = fopen("{$path}/.htaccess", 'w+');
            if(is_resource($fsource)){
                fwrite($fsource, "");
                fclose($fsource);
            }
        }
    }

}