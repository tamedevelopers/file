<?php

declare(strict_types=1);

namespace Tamedevelopers\File;

use Tamedevelopers\File\Traits\CommonTrait;
use Tamedevelopers\File\Traits\FileMagicTrait;


class ImageAutoresize {	
	
    use FileMagicTrait, CommonTrait;

    /**
     * Resize an image based on width and/or height.
     *
     * @param int|null $width The desired width of the resized image (in pixels).
     * @param int|null $height The desired height of the resized image (in pixels).
     * 
     * @return bool 
     * - Returns true on success or false on failure.
     */
    public function resize($width = null, $height = null)
    {
        $this->loop(function($response) use($width, $height) {
            foreach($response->uploads as $upload){

                // resource
                $imageSource = $this->getImageResource($upload);

                // GdImage object
                $gdImage = $response->createGDImage($imageSource, $upload['fullPath']);
                
                // Get the dimensions of the source image.
                $source_width = imagesx($gdImage);
                $source_height = imagesy($gdImage);
                
                // get image aspect ratio
                [$width, $height] = $this->imageAspectRatio($imageSource, $width, $height);

                // Create a new image with the adjusted dimensions
                $resizedImage = @imagecreatetruecolor($width, $height);

                // Check if the destination image resource was created successfully
                if ($resizedImage) {
                    $white_color = imagecolorallocate($resizedImage, 255, 255, 255);
                    imagefill($resizedImage, 0, 0, $white_color);
                    imagecopyresampled($resizedImage, $gdImage, 0, 0, 0, 0, $width, $height, $source_width, $source_height);
                }

                // save copy of image
                $this->saveImage($resizedImage, $upload['fullPath']);

                // run bucket method 
                $this->bucket($upload);
            }
        });
    }  

    /**
     * Automatically adjusts image size to fit into the provided dimensions.
     *
     * @param string $imageSource The path to the image file.
     * @param int $width The desired width of the image.
     * @param int $height The desired height of the image.
     * 
     * @return array 
     * - An array containing the adjusted width and height of the image.
     */
    private function imageAspectRatio($imageSource, $width, $height) 
    {
        // Get the original size of the image
        list($original_width, $original_height) = $imageSource;

        // Calculate the aspect ratio of the original image
        $aspect_ratio = $original_width / $original_height;

        // If both width and height are provided, adjust the image to fit
        if ($width && $height) {
            // Calculate the aspect ratio of the desired dimensions
            $desired_aspect_ratio = $width / $height;

            // If the original aspect ratio is wider than the desired aspect ratio, adjust the width
            if ($aspect_ratio > $desired_aspect_ratio) {
                $height = $width / $aspect_ratio;
            }
            // If the original aspect ratio is taller than the desired aspect ratio, adjust the height
            else {
                $width = $height * $aspect_ratio;
            }
        }
        // If only width is provided, adjust the height to maintain aspect ratio
        elseif ($width) {
            $height = $width / $aspect_ratio;
        }
        // If only height is provided, adjust the width to maintain aspect ratio
        elseif ($height) {
            $width = $height * $aspect_ratio;
        }
        // If no dimensions are provided, return the original size of the image
        else {
            return array(
                $original_width,
                $original_height
            );
        }

        // Return the adjusted dimensions as an array
        return array(
            (int) round($width),
            (int) round($height)
        );
    }

    /**
     * Save an image resource to a file
     *
     * @param mixed $imageSource The image resource to save
     * @param string $filePath The path to save the image
     * 
     * @return void
     */
    private function saveImage($imageSource, $filePath) 
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // Save as JPEG
                @imagejpeg($imageSource, $filePath);
                break;
            case 'png':
                // Save as PNG
                @imagepng($imageSource, $filePath);
                break;
            case 'gif':
                // Save as GIF
                @imagegif($imageSource, $filePath);
                break;
            case 'webp':
                // Save as WebP
                @imagewebp($imageSource, $filePath);
                break;
        }

        // Free up memory.
        imagedestroy($imageSource);
    }

	
}
