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
     * Get File Type
     *
     * @return string
     */
    public function type()
    {
        return $this->data['type'];
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
     * Determine whether the uploaded file is a valid image.
     *
     * This method validates the file by:
     * - Checking that a MIME type exists
     * - Ensuring the MIME type starts with "image/"
     * - Verifying the file can be read by getimagesize()
     *
     * This helps prevent spoofed extensions (e.g. renamed .exe to .jpg).
     *
     * @return bool
     *  True if the file is a valid image, otherwise false.
     */
    public function isImage()
    {
        $mime = $this->mime();

        return $mime && str_starts_with($mime, 'image/') 
            && @getimagesize($this->tmp()) !== false;
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