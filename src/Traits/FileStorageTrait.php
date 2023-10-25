<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Traits;

use Tamedevelopers\Support\Tame;
use Tamedevelopers\Support\Time;
use Tamedevelopers\Support\Server;


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

        // if directory is empty
        $uploadDirectory = empty($uploadDirectory) ? "" : "{$uploadDirectory}/";

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
                $fullPath   = "{$this->config['baseDir']}{$uploadDirectory}{$newGenFileName}";
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
     * Create Base Directory
     *
     * @return void
     */
    protected function createBaseDirectory()
    {
        $baseDirectory = $this->config['baseDir'];

        if(!is_dir($baseDirectory)){
            @mkdir($baseDirectory, 0777);

            // Create index file
            $this->createDefaultRestrictedFiles($baseDirectory);
        }
    }
    
    /**
     * Create Non Existable Base Folder
     *
     * @param  mixed $uploadDirectory
     * @return void
     */
    protected function createParentFolder($uploadDirectory)
    {
        if(!empty($uploadDirectory)){
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

        // if directory is empty
        $uploadDirectory = empty($uploadDirectory) ? "" : "{$uploadDirectory}/";

        $time = [
            "year"  => Time::timestamp($now, 'Y'),
            "month" => Time::timestamp($now, 'n'),
            "day"   => Time::timestamp($now, 'j'),
            "now"   => $now
        ];

        return [
            'year'  => "{$this->config['baseDir']}{$uploadDirectory}{$time['year']}",
            'month' => "{$this->config['baseDir']}{$uploadDirectory}{$time['year']}/{$time['month']}",
            'day'   => "{$this->config['baseDir']}{$uploadDirectory}{$time['year']}/{$time['month']}/{$time['day']}", 
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
        $dummyPath = $this->pathToDummy($path);
        
        // create for htaccess 
        $this->createHtaccess($dummyPath);
        
        // create for html 
        // $this->createHtml($dummyPath);
    }
    
    /**
     * Create HTaccess File
     *
     * @param  mixed $dummyPath
     * @return void
     */
    private function createHtaccess($dummyPath)
    {
        // create for htaccess 
        if(!Tame::exists($dummyPath['htaccess']['path'])){
            // Read the contents of the dummy file
            $dummyContent = file_get_contents($dummyPath['htaccess']['dummy']);

            // Write the contents to the new file
            file_put_contents($dummyPath['htaccess']['path'], $dummyContent);
        }
    }
    
    /**
     * Create HTML File
     *
     * @param  mixed $dummyPath
     * @return void
     */
    private function createHtml($dummyPath)
    {
        // create for html 
        if(!Tame::exists($dummyPath['html']['path'])){
            // Read the contents of the dummy file
            $dummyContent = file_get_contents($dummyPath['html']['dummy']);

            // Write the contents to the new file
            file_put_contents($dummyPath['html']['path'], $dummyContent);
        }
    }
    
    /**
     * Path to dummy files
     * @param  mixed $path
     * @return array
     */
    private function pathToDummy($path)
    {
        $packageDummyPath = Server::cleanServerPath(
            dirname(__DIR__) . "/Dummy/"
        );
        
        return [
            'htaccess'  => [
                'path'  => "{$path}/.htaccess",
                'dummy' => "{$packageDummyPath}dummyHtaccess.dum",
            ],
            'html'      => [
                'path'  => "{$path}/index.html",
                'dummy' => "{$packageDummyPath}dummyHtml.dum",
            ]
        ];
    }

}