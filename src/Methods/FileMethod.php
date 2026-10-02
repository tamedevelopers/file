<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Methods;

/**
 * @property  mixed $file
 */
abstract class FileMethod{
    
    /**
     * Check if File Input is set
     *
     * @param string|null $name
     * @return bool
     */
    static public function fileIsset($name = null)
    {
        return isset($_FILES[self::getFileName($name)]);
    }
    
    /**
     * Alias for fileIsset
     *
     * @param string|null $name
     * @return bool
     */
    static public function isset($name = null)
    {
        return self::fileIsset($name);
    }
    
    /**
     * Check if File Input is not empty
     *
     * @param string|null $name
     * @return bool
     */
    static public function notEmpty($name = null)
    {
        $fileName = self::getFileName($name);

        if(isset($_FILES[$fileName]) && !empty($_FILES[$fileName]['name'][0])){
            return true;
        }

        return false;
    }

    /**
     * Check if File Input is Empty
     *
     * @param string|null $name
     * @return bool
     */
    static public function isEmpty($name = null)
    {
        return ! self::notEmpty($name);
    }

    /**
     * Check if File Input not empty
     *
     * @param string|null $name
     * @return bool
     */
    static public function isNotEmpty($name = null)
    {
        return self::notEmpty($name);
    }

    /**
     * Check if File Input not empty
     *
     * @param string|null $name
     * @return bool
     */
    static public function has($name = null)
    {
        return self::notEmpty($name);
    }

    /**
     * Get File Name
     *
     * @param string|null $name
     * @return string|null
     */
    static protected function getFileName($name = null)
    {
        return empty($name) 
            ? static::$file->getName()
            : $name;
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
     * @param array $mimeTypes
     * @return array
     */
    protected static function excludeTypes($extensions, $mimeTypes = [])
    {
        return static::filterTypes($extensions, $mimeTypes, true);
    }

    /**
     * Include only specific mime types and extensions by raw extension keys (e.g. png, pdf)
     * 
     * @param array $extensions
     * @param array $mimeTypes
     * @return array
     */
    protected static function includeTypes($extensions, $mimeTypes = [])
    {
        return static::filterTypes($extensions, $mimeTypes, false);
    }

    /**
     * Filter mime types and extensions for the configured mime group.
     *
     * @param array $extensions Raw extensions without dots (e.g., ['png', 'pdf'])
     * @param array $mimeTypes
     * @param bool $isExclude True to exclude, false to include
     * @return array
     */
    protected static function filterTypes($extensions, $mimeTypes = [], $isExclude = true)
    {
        $allowed = !empty($mimeTypes) ? $mimeTypes : static::allowedMimeType();
        $types   = static::baseTypes();
        $mime    = static::$file->config['mime'] ?? null;

        // If configured mime group doesn't exist, return base allowed types
        if (!$mime || !isset($allowed['extension'][$mime])) {
            return $allowed;
        }

        $targetExts  = [];
        $targetMimes = [];

        // Collect matching extensions and MIME types directly
        foreach ($types as $type) {
            foreach ($type['map'] as $ext => $mimes) {
                if (in_array($ext, $extensions, true)) {
                    $targetExts[] = '.' . $ext;
                    $targetMimes  = array_merge($targetMimes, $mimes);
                }
            }
        }

        if ($isExclude) {
            // Exclude specified extensions and mimes
            $allowed['extension'][$mime] = array_values(array_diff($allowed['extension'][$mime], $targetExts));
            $allowed['mime'][$mime]      = array_values(array_diff($allowed['mime'][$mime], $targetMimes));
        } else {
            // Merge and de-duplicate to append target extensions into the active mime group
            $allowed['extension'][$mime] = array_values(array_unique(array_merge($allowed['extension'][$mime], $targetExts)));
            $allowed['mime'][$mime]      = array_values(array_unique(array_merge($allowed['mime'][$mime], $targetMimes)));
        }

        return $allowed;
    }
    
}