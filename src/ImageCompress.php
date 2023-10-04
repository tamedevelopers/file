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
                
                // save copy of image
                $this->saveImage($gdImage, $upload['fullPath'], $quality);

                // run bucket method 
                $this->bucket($upload);
            }
        });
    }

    /**
     * Save an image resource to a file
     *
     * @param mixed $gdImage The image resource to save
     * @param string $filePath The path to save the image
     * @param int $quality The image quality (0-100, applicable for JPEG and WebP)
     * 
     * @return void
     */
    public function saveImage($gdImage, $filePath, $quality = 50)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // Save as JPEG
                @imagejpeg($gdImage, $filePath, 50);
                break;
            case 'png':
                // Save as PNG
                $this->allowBlending($gdImage);
                $value = (int) round(9 * (100 - $quality) / 90);
                @imagepng($gdImage, $filePath, $value);
                break;
            case 'gif':
                // Save as GIF
                $this->allowBlending($gdImage);
                @imagegif($gdImage, $filePath);
                break;
            case 'webp':
                // Save as WebP
                $this->allowBlending($gdImage);
                @imagewebp($gdImage, $filePath, $quality);
                break;
        }

        // Free up memory.
        imagedestroy($gdImage);
    }

    /**
     * Apply blending mode
     *
     * @param mixed $gdImage The image resource to save
     * @return void
     */
    private function allowBlending($gdImage)
    {
        if($this->isImageTransparent($gdImage)){
            imageAlphaBlending($gdImage, true);
            imageSaveAlpha($gdImage, true);
        }
    }

    /**
	 * Check if image is transparent
	 *
	 * @param  mixed $im
	 * @return bool
	 */
	private function isImageTransparent($gdImage) 
	{
		$width  = imagesx($gdImage); // Get the width of the image
		$height = imagesy($gdImage); // Get the height of the image

		// We run the image pixel by pixel and as soon as we find a transparent pixel we stop and return true.
		for($i = 0; $i < $width; $i++) {
			for($j = 0; $j < $height; $j++) {
				$rgba = imagecolorat($gdImage, $i, $j);
				if(($rgba & 0x7F000000) >> 24) {
					return true;
				}
			}
		}

		return false;
	}
	
}
