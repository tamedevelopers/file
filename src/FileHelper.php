<?php

namespace Tamedevelopers\File;

use Tamedevelopers\Support\Str;
use Tamedevelopers\File\Methods\FileMethod;


class FileHelper extends FileMethod{
        
    /**
     * Upload Data
     *
     * @var mixed
     */
    private $data;

    /**
     * __construct
     *
     * @param  mixed $data
     * @return void
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }
    
    /**
     * Get File Extension
     *
     * @return string
     */
    public function extension()
    {
        return Str::lower(pathinfo(
            $this->data['full_path'], 
            PATHINFO_EXTENSION)
        );
    }
    
    /**
     * Get Temporary Path
     *
     * @return string
     */
    public function tmp()
    {
        return $this->data['tmp_name'] ?? 'null';
    }
    
    /**
     * Get File Name
     *
     * @return string
     */
    public function name()
    {
        return $this->data['full_path'];
    }
        
    /**
     * Get File MimeType
     *
     * @return string|bool
     * - false if not found
     */
    public function mime()
    {
        return Str::lower(@mime_content_type(
            $this->tmp()
        ));
    }

    /**
     * Get File Size
     * 
     * @return int
     */
    public function size()
    {
        return $this->data['size'];
    }

    /**
     * Get Image Size
     * 
     * @return array
     * [width, height]
     */
    public function imageSize()
    {
        // get image attributes
        $imagePath = @getimagesize($this->tmp());

        return [
            'width'  => $imagePath[0] ?? null,
            'height' => $imagePath[1] ?? null
        ];
    }
       
    /**
     * Generate new file
     *
     * @param  bool $allow
     * @return string
     * - Generated Filename
     */
    public function generate(?bool $allow = true)
    {
        // if not allowed
        // return the default file `full_path` name
        if(!$allow){
            return $this->name();
        }

        return bin2hex(random_bytes(25)) . '.' . $this->extension();
    }

}