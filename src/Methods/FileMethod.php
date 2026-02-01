<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Methods;

use Tamedevelopers\File\FileHelper;
use Tamedevelopers\Support\Collections\Collection;


abstract class FileMethod{
    
    /**
     * Check if File Input is set
     *
     * @param string $name
     * @return bool
     */
    static public function fileIsset($name)
    {
        if(isset($_FILES[$name])){
            return true;
        }

        return false;
    }
    
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
     * @return array|null
     */
    static protected function getUploads($uploads, $first = false)
    {
        if($first){
            if(self::isArray($uploads)){
                $uploads = $uploads[0];
                return [
                    'name'  => $uploads['name'],
                    'path'  => $uploads['path'],
                    'url'   => $uploads['urlPath'],
                ];
            }
        } elseif(self::isArray($uploads)){
            $files = [];
            foreach($uploads as $key => $upload){
                $files[$key] = [
                    'name'  => $upload['name'],
                    'path'  => $upload['path'],
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
    static protected function numericToInt($value = null)
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
            'year', 'month', 'day' => $mode,
            default => 'default',
        };
    }

    /**
     * Base Mime Types and Extension Types
     */
    protected static function baseTypes(): array
    {
        return [
            'video' => [
                'map' => [
                    'mp4'   => ['video/mp4'],
                    'mpeg'  => ['video/mpeg'],
                    'mov'   => ['video/quicktime'],
                    'avi'   => ['video/x-msvideo'],
                    'wmv'   => ['video/x-ms-wmv'],
                ],
            ],

            'audio' => [
                'map' => [
                    'mp3' => ['audio/mpeg'],
                    'wav' => ['audio/x-wav'],
                ],
            ],

            'image' => [
                'map' => [
                    'jpg'  => ['image/jpeg'],
                    'jpeg' => ['image/jpeg'],
                    'png'  => ['image/png'],
                    'gif'  => ['image/gif'],
                ],
            ],

            'image_others' => [
                'map' => [
                    'webp' => ['image/webp'],
                    'ico'  => ['image/vnd.microsoft.icon'],
                ],
            ],

            'document' => [
                'map' => [
                    'pdf'  => ['application/pdf'],
                    'txt'  => ['text/plain'],
                    'doc'  => ['application/msword'],
                    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                ],
            ],

            'spreadsheet' => [
                'map' => [
                    'xls'  => ['application/vnd.ms-excel'],
                    'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                    'csv'  => ['text/csv'],
                ],
            ],

            'archive' => [
                'map' => [
                    'zip' => [
                        'application/zip',
                        'application/x-zip-compressed',
                        'multipart/x-zip',
                    ],
                    'rar' => [
                        'application/x-rar-compressed',
                        'application/octet-stream',
                    ],
                ],
            ],
        ];
    }

    /**
     * MimeType Groups
     */
    protected static function groups(): array
    {
        return [
            'image'         => ['image'],
            'video'         => ['video'],
            'audio'         => ['audio'],
            'file'          => ['document'],
            'general_image' => ['image', 'image_others'],
            'general_media' => ['audio', 'video'],
            'general_file'  => ['document', 'spreadsheet', 'archive'],
            'pdf'           => ['document'],
            'doc'           => ['document'],
            'xls'           => ['spreadsheet'],
            'zip'           => ['archive'],
        ];
    }

    /**
     * Allowed MimeType and Extension Types
     */
    static protected function allowedMimeType(): array
    {
        $types  = static::baseTypes();
        $groups = static::groups();

        $result = ['mime' => [], 'extension' => []];

        foreach ($groups as $group => $typeKeys) {
            foreach ($typeKeys as $typeKey) {
                foreach ($types[$typeKey]['map'] as $ext => $mimes) {
                    $result['extension'][$group][] = '.' . $ext;
                    $result['mime'][$group] = array_merge(
                        $result['mime'][$group] ?? [],
                        $mimes
                    );
                }
            }

            $result['extension'][$group] = array_values(array_unique($result['extension'][$group]));
            $result['mime'][$group]      = array_values(array_unique($result['mime'][$group]));
        }

        return $result;
    }

    /**
     * Exclude mime types and extensions by raw extension keys (e.g. png, pdf)
     *
     * @param array $extensions
     * @return array
     */
    protected static function excludeTypes(array $extensions): array
    {
        $allowed = static::allowedMimeType();
        $types   = static::baseTypes();

        $excludeExt  = [];
        $excludeMime = [];

        foreach ($types as $type) {
            foreach ($type['map'] as $ext => $mimes) {
                if (in_array($ext, $extensions, true)) {
                    $excludeExt[]  = '.' . $ext;
                    $excludeMime = array_merge($excludeMime, $mimes);
                }
            }
        }

        foreach ($allowed['extension'] as $group => $exts) {
            $allowed['extension'][$group] = array_values(array_diff($exts, $excludeExt));
        }

        foreach ($allowed['mime'] as $group => $mimes) {
            $allowed['mime'][$group] = array_values(array_diff($mimes, $excludeMime));
        }

        return $allowed;
    }
    
}