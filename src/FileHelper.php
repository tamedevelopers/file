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
     */
    public function extension(): string
    {
        return Str::lower(pathinfo(
            $this->data['full_path'], 
            PATHINFO_EXTENSION)
        );
    }
    
    /**
     * Get File Extension with dot (.jpg)
     */
    public function extensionWithDot(): string
    {
        return ".{$this->extension()}";
    }
    
    /**
     * Get Temporary Path
     */
    public function tmp(): string
    {
        return $this->data['tmp_name'] ?? 'null';
    }
    
    /**
     * Get File Type
     */
    public function type(): string
    {
        return $this->data['type'];
    }
    
    /**
     * Get File Name
     */
    public function name(): string
    {
        return $this->data['full_path'];
    }
        
    /**
     * Get File MimeType
     */
    public function mime(): string|bool
    {
        return Str::lower(@mime_content_type(
            $this->tmp()
        ));
    }

    /**
     * Get File Size
     */
    public function size(): int
    {
        return $this->data['size'];
    }

    /**
     * Get Image Size
     * 
     * @return array{width: mixed, height: mixed}
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
     * @return string       Generated Filename
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