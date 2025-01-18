<?php

namespace Tamedevelopers\File\Traits;


trait CommonTrait{

    /**
     * Save an image resource to a file
     *
     * @param \GdImage $gdImage The image resource to save
     * @param string $filePath The path to save the image
     * @param int $quality The image quality (0-100, applicable for JPEG and WebP)
     * 
     * @return void
     */
    private function saveImage($gdImage, $filePath, $quality = 50)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $quality = (int) $quality;
        $quality = $quality < 50 || $quality > 100 ? 50 : $quality;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // Save as JPEG
                @imagejpeg($gdImage, $filePath, $quality);
                break;
            case 'png':
                // Save as PNG
                $this->allowBlending($gdImage);
                @imagepng($gdImage, $filePath);
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
     * Get the background color of the source image.
     *
     * @param \GdImage $gdImage The GD image resource of the source image.
     * @return array An array representing the RGB color values.
     */
    private function getBackgroundColor($gdImage)
    {
        // Get the color of the top-left pixel (position 0, 0) of the source image
        $backgroundColor = imagecolorat($gdImage, 0, 0);

        // Extract the red, green, and blue components
        $red    = ($backgroundColor >> 16) & 0xFF;
        $green  = ($backgroundColor >> 8) & 0xFF;
        $blue   = $backgroundColor & 0xFF;

        return [$red, $green, $blue];
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

    /**
     * Only ignore if validate metho, has been manually called
     *
     * @return mixed
     */
    public function ignoreIfValidatorHasBeenCalled()
    {
        if(!$this->isValidatedCalled){
            $this->validate();
        }
    }
    
    /**
     * Check if is local driver
     *
     * @return bool
     */
    public function isLocalDriver()
    {
        if(isset($this->config['driver']) && $this->config['driver'] === 'local'){
            return true;
        }

        return false;
    }

}