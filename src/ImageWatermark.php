<?php

declare(strict_types=1);

namespace Tamedevelopers\File;

use Tamedevelopers\File\ImageCompress;
use Tamedevelopers\File\Traits\CommonTrait;
use Tamedevelopers\File\Traits\FileMagicTrait;


class ImageWatermark {	
	
    use FileMagicTrait, CommonTrait;

    /**
     * Add a watermark to an image.
     *
     * @param string $watermarkSource The path to the watermark image.
     * 
     * @param string $position The position of the watermark. 
     * - Possible values are ['center', 'bottom-right', 'bottom-left', 'top-right', 'top-left'. Default is 'bottom-right'].
     * 
     * @param  int $padding Padding in pixels (applied to all positions except 'center')
     * 
     * @return bool True if the watermark was successfully added, false otherwise.
     */
    public function watermark($watermarkSource = null, $position = 'bottom-right', $padding = 15)
    {
        $this->loop(function($response) use($watermarkSource, $position, $padding){
            foreach($response->uploads as $upload){

                // resource
                $imageSource = $this->getImageResource($upload);

                // GdImage object
                $gdImage = $response->createGDImage($imageSource, $upload['fullPath']);
                
                // Get the dimensions of the source image.
                $source_width = imagesx($gdImage);
                $source_height = imagesy($gdImage);

                // full path to watermark image
                $watermarkImage = base_path($watermarkSource);

                // watermark GdImage object
                $watermarkGDImage = $response->createGDImage(getimagesize($watermarkImage), $watermarkImage);

                // Get the dimensions of the watermark image.
                $watermark_width    = imagesx($watermarkGDImage);
                $watermark_height   = imagesy($watermarkGDImage);

                // apply watermark to image
                $check = $this->applyWaterMark(
                    position:           $position,
                    source_width:       $source_width,
                    source_height:      $source_height,
                    watermark_width:    $watermark_width,
                    watermark_height:   $watermark_height,
                    imageSource:        $gdImage,
                    watermarkSource:    $watermarkGDImage,
                    padding:            $padding,
                );

                // save image to path
                if($check){
                    (new ImageCompress)->saveImage(
                        gdImage: $gdImage,
                        filePath: $upload['fullPath']
                    );

                    // run bucket method 
                    $this->bucket($upload);
                }
            }
        });
    }
    
    /**
     * Apply WaterMark
     *
     * @param  mixed $imageSource
     * @param  mixed $watermarkSource
     * @param  mixed $position
     * @param  mixed $source_width
     * @param  mixed $source_height
     * @param  mixed $watermark_width
     * @param  mixed $watermark_height
     * @param  int $padding Padding in pixels (applied to all positions except 'center')
     * 
     * @return bool
     */
    private function applyWaterMark($imageSource, $watermarkSource, $position, $source_width, $source_height, $watermark_width, $watermark_height, $padding)
    {
        // Determine the position of the watermark.
        switch ($position) {
            case 'center':
                $watermark_x = ($source_width - $watermark_width) / 2;
                $watermark_y = ($source_height - $watermark_height) / 2;
                break;
            case 'top-right':
                $watermark_x = $source_width - $watermark_width - $padding;
                $watermark_y = $padding;
                break;
            case 'top-left':
                $watermark_x = $padding;
                $watermark_y = $padding;
                break;
            case 'bottom-left':
                $watermark_x = $padding;
                $watermark_y = $source_height - $watermark_height - $padding;
                break;
            case 'bottom-right':
            default:
                $watermark_x = $source_width - $watermark_width - $padding;
                $watermark_y = $source_height - $watermark_height - $padding;
                break;
        }

        // Add the watermark to the source image.
        if (!imagecopy($imageSource, $watermarkSource, (int) $watermark_x, (int) $watermark_y, 0, 0, (int) $watermark_width, (int) $watermark_height)) {
            return false;
        }

        return true;
    }
	
}
