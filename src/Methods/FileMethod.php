<?php

declare(strict_types=1);

namespace Tamedevelopers\File\Methods;

use Tamedevelopers\Support\Str;

/**
 * Abstract FileMethod Class
 *
 * @method static bool fileIsset(string|null $name = null) Check if a file input key exists in$_FILES
 * @method static bool isset(string|null $name = null) Alias for fileIsset()
 * @method static bool notEmpty(string|null $name = null) Check if a file input has uploaded files
 * @method static bool isEmpty(string|null $name = null) Check if a file input is empty or not uploaded
 * @method static bool isNotEmpty(string|null $name = null) Check if a file input has uploaded files
 * @method static bool has(string|null $name = null) Check if a file input has uploaded files
 * 
 * @method bool fileIsset(string|null $name = null) Check if a file input key exists in$_FILES
 * @method bool isset(string|null $name = null) Alias for fileIsset()
 * @method bool notEmpty(string|null $name = null) Check if a file input has uploaded files
 * @method bool isEmpty(string|null $name = null) Check if a file input is empty or not uploaded
 * @method bool isNotEmpty(string|null $name = null) Check if a file input has uploaded files
 * @method bool has(string|null $name = null) Check if a file input has uploaded files
 * 
 * @property mixed $instances
 */
abstract class FileMethod{

    /**
     * Handle dynamic instance method calls.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array$arguments)
    {
        $protectedMethod = 'protected' . ucfirst($method);

        if (method_exists($this, $protectedMethod)) {
            return $this->{$protectedMethod}(...$arguments);
        }

        throw new \BadMethodCallException("Method {$method} does not exist on " . static::class);
    }

    /**
     * Handle dynamic static method calls.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $method, array$arguments)
    {
        $protectedMethod = 'protected' . ucfirst($method);
        $name = $arguments[0] ?? null;

        $instance = static::resolveStaticInstance($name);

        if (method_exists($instance,$protectedMethod)) {
            return $instance->{$protectedMethod}(...$arguments);
        }

        throw new \BadMethodCallException("Static method {$method} does not exist on " . static::class);
    }

    /**
     * Resolve or instantiate a File object for static execution.
     *
     * @param string|null $name
     * @return static
     */
    protected static function resolveStaticInstance(?string $name = null)
    {
        if (!empty($name) && isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        return new static($name);
    }

    /**
     * Check if a specific file input key exists in the $_FILES array.
     *
     * @param string|null $name Field name or null to use current instance name
     * @return bool
     */
    protected function protectedFileIsset($name = null): bool
    {
        return isset($_FILES[$this->getFileName($name)]);
    }

    /**
     * Alias for `protectedFileIsset()`.
     *
     * @param string|null $name Field name or null to use current instance name
     * @return bool
     */
    protected function protectedIsset($name = null): bool
    {
        return $this->protectedFileIsset($name);
    }

    /**
     * Check if a file input has uploaded files (supports single and multi-file inputs).
     *
     * @param string|null $name Field name or null to use current instance name
     * @return bool
     */
    protected function protectedNotEmpty($name = null): bool
    {
        $fileName = $this->getFileName($name);

        if (empty($fileName) || !isset($_FILES[$fileName]['name'])) {
            return false;
        }

        $fileNames = $_FILES[$fileName]['name'];

        if (self::isArray($fileNames)) {
            return !empty(Str::head($fileNames));
        }

        return !empty($fileNames);
    }

    /**
     * Check if a file input is empty or has no uploaded files.
     *
     * @param string|null $name Field name or null to use current instance name
     * @return bool
     */
    protected function protectedIsEmpty($name = null): bool
    {
        return !$this->protectedNotEmpty($name);
    }

    /**
     * Check if a file input has uploaded files (alias for `notEmpty`).
     *
     * @param string|null $name Field name or null to use current instance name
     * @return bool
     */
    protected function protectedIsNotEmpty($name = null): bool
    {
        return $this->protectedNotEmpty($name);
    }

    /**
     * Check if a file input has uploaded files (alias for `notEmpty`).
     *
     * @param string|null $name Field name or null to use current instance name
     * @return bool
     */
    protected function protectedHas($name = null): bool
    {
        return $this->protectedNotEmpty($name);
    }

    /**
     * Resolve the active input field name.
     *
     * @param string|null $name
     * @return string|null
     */
    protected function getFileName($name = null): ?string
    {
        return empty($name) ? $this->getName() :$name;
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