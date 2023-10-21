<?php

declare(strict_types=1);

namespace Tamedevelopers\File;

use Tamedevelopers\File\Traits\CommonTrait;
use Tamedevelopers\File\Traits\FileMagicTrait;


class ImageCompress{	
	
    use FileMagicTrait, CommonTrait;

    /**
     * Compress an image file.
     * 
     * @param int $quality The quality of compression (0 to 100).
     * 
     * @return bool 
     * Returns true on success or false on failure.
     */
    public function compress($quality = 50)
    {
        $this->loop(function($response) use($quality) {
            foreach($response->uploads as $upload){

                // resource
                $imageSource = $this->getImageResource($upload);

                // GdImage object
                $gdImage = $response->createGDImage($imageSource, $upload['fullPath']);

                // if not an instance of GdImage
                if(!$gdImage instanceof \GdImage){
                    return;
                }
                
                // save copy of image
                $this->saveImage(
                    $gdImage, 
                    $upload['fullPath'], 
                    $quality
                );

                // run bucket method 
                $this->bucket($upload);
            }
        });
    }
	
}
