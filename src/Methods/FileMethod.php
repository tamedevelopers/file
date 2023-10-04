<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Methods;

use Tamedevelopers\File\FileHelper;
use Tamedevelopers\Support\Collections\Collection;


abstract class FileMethod{
    
    /**
     * Check if File Input is not empty
     *
     * @param string $name
     * @return bool
     */
    static public function notEmpty($name)
    {
        if(isset($_FILES[$name]) && !empty($_FILES[$name]['name'][0])){
            return true;
        }

        return false;
    }

    /**
     * Check if File Input is Empty
     *
     * @param string $name
     * @return bool
     */
    static public function isEmpty($name)
    {
        return ! self::notEmpty($name);
    }

    /**
     * Check if File Input not empty
     *
     * @param string $name
     * @return bool
     */
    static public function isNotEmpty($name)
    {
        return self::notEmpty($name);
    }

    /**
     * Check if File Input not empty
     *
     * @param string $name
     * @return bool
     */
    static public function has($name)
    {
        return self::notEmpty($name);
    }
     
    /**
     * Get Uploads
     *
     * @param  mixed $uploads
     * @param  bool $first
     * @return void
     */
    static protected function getUploads($uploads, $first = false)
    {
        if($first){
            if(self::isArray($uploads)){
                $uploads = $uploads[0];
                return [
                    'name'  => $uploads['path'],
                    'url'   => $uploads['urlPath'],
                ];
            }
        } elseif(self::isArray($uploads)){
            $files = [];
            foreach($uploads as $key => $upload){
                $files[$key] = [
                    'name'  => $upload['path'],
                    'url'   => $upload['urlPath'],
                ];
            }

            return $files;
        }
    }
    
    /**
     * Rearrange File
     *
     * @param  mixed $files
     * @return array
     */
    static protected function rearrangeFiles($files)
    {
        $rearrangedFiles = [];

        // we loop through the main $_FILES
        foreach ($files as $fileKey => $fileData) {

            // loop through it's data, which is an array as well
            foreach ($fileData as $index => $value) {

                // first we'll check if the value is an array
                // form input becomes an array when selecting multiple
                // name="input[]"
                if(self::isArray($value)){

                    // arrange the file data by index counter
                    for($i = 0; $i < count($value); $i++) {
                        $rearrangedFiles[$fileKey][$i][$index] = $value[$i];
                    }

                } else{
                    $rearrangedFiles[$fileKey][0] = $fileData;
                }
            }
        }

        return $rearrangedFiles;
    }
    
    /**
     * Convert To File Helper Object
     * 
     * @return mixed
     */
    static protected function convertToFileHelper()
    {
        $arrangedFiles = self::rearrangeFiles($_FILES);

        $convertedFiles = [];

        // Now we are looping through the already arranged files
        foreach ($arrangedFiles as $fileKey => $fileData) {

            // lets check if the array is greater than 1
            // if the count is more than one element in the array
            // this means we have multiple array, in it's collection
            // else we convert from index to parent FileHelper
            if(count($fileData) > 1){
                // loop through it's data
                foreach ($fileData as $index => $value) {
                    $convertedFiles[$fileKey][$index] = new FileHelper($value);
                }
            } else{
                // if upload is not empty
                if(!empty($fileData[0]['name'])){
                    $convertedFiles[$fileKey][0]  = new FileHelper($fileData[0]);
                }
            }
        }
        
        return new Collection($convertedFiles);
    }
    
    /**
     * Check value is an array
     *
     * @param  mixed $value
     * @return bool
     */
    static protected function isArray($value = null)
    {
        return is_array($value);
    }
    
    /**
     * Convert numeric to int
     *
     * @param  mixed $value
     * @return mixed
     */
    static protected function numbericToInt($value = null)
    {
        if(is_numeric($value)){
            $value = (int) trim((string) $value);
        }

        return $value;
    }
    
    /**
     * Set Structure Type
     *
     * @param  string|null $mode
     * @return string
     */
    static protected function getStructureType($mode = null)
    {
        return match ($mode) {
            'default', 'year', 'month', 'day' => $mode,
            default => 'default',
        };
    }

    /**
     * Allowed MimeType and Extension Types
     *
     * @return array
     */
    static protected function allowedMimeType()
    {
        // Extension MimeType
        $mimeType = [
            'video'         =>  ['video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/x-ms-wmv'],
            'audio'         =>  ['audio/mpeg','audio/x-wav'],
            'files'         =>  ['application/msword','application/pdf','text/plain'],
            'images'        =>  ['image/jpeg', 'image/png', 'image/gif'],
            'general_image' =>  ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/vnd.microsoft.icon'],
            'general_media' =>  ['audio/mpeg','audio/x-wav', 'video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/x-ms-wmv'],
            'general_file'  =>  [
                'application/msword','application/pdf','text/plain','application/zip', 'application/x-zip-compressed', 'multipart/x-zip',
                'application/x-zip-compressed', 'application/x-rar-compressed', 'application/octet-stream', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        ];  

        // Extension Type
        $extensionType = [
            'video'         =>  ['.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
            'audio'         =>  ['.mp3', '.wav'],
            'files'         =>  ['.docx', '.pdf', '.txt'],
            'images'        =>  ['.jpg', '.jpeg', '.png', '.gif'],
            'general_image' =>  ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.ico'],
            'general_media' =>  ['.mp3', '.wav', '.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
            'general_file'  =>  ['.docx', '.pdf', '.txt', '.zip', '.rar', '.xlsx', '.xls'],
        ];
        
        return ['mime' => $mimeType, 'extension' => $extensionType];
    }
    
}