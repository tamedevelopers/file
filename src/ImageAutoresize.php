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
        $this->loop(function ($response) use ($width, $height) {
            foreach ($response->uploads as $upload) {
                // Resource
                $imageSource = $this->getImageResource($upload);

                // GdImage object
                $gdImage = $response->createGDImage($imageSource, $upload['fullPath']);

                // If not an instance of GdImage
                if (!$gdImage instanceof \GdImage) {
                    return;
                }

                // Get the dimensions of the source image.
                $source_width = imagesx($gdImage);
                $source_height = imagesy($gdImage);

                // Calculate the closest possible fit dimensions
                [$new_width, $new_height] = $this->imageAspectRatio(
                    [$source_width, $source_height],
                    $width,
                    $height
                );

                // Create a new image with the adjusted dimensions
                $resizedImage = @imagecreatetruecolor($new_width, $new_height);

                // Check if the destination image resource was created successfully
                if ($resizedImage) {
                    $this->prepareImageForTransparency($gdImage, $resizedImage, $upload);

                    // Perform the image resizing operation
                    imagecopyresampled(
                        $resizedImage,
                        $gdImage,
                        0,
                        0,
                        0,
                        0,
                        $new_width,
                        $new_height,
                        $source_width,
                        $source_height
                    );
                }
                
                // Save a copy of the resized image
                $this->saveImage(
                    $resizedImage, 
                    $upload['fullPath'], 
                    60
                );

                // Run bucket method for further processing
                $this->bucket($upload);
            }
        });
    }

    /**
     * Automatically adjusts image size to fit into the provided dimensions.
     *
     * @param array $imageSource An array with original width and height.
     * @param int|null $max_width The maximum desired width.
     * @param int|null $max_height The maximum desired height.
     * 
     * @return array
     * - An array containing the adjusted width and height of the image.
     */
    private function imageAspectRatio(array $imageSource, $max_width, $max_height)
    {
        [$original_width, $original_height] = $imageSource;

        // Aspect ratio of the original image
        $aspect_ratio = $original_width / $original_height;

        if ($max_width && $max_height) {
            // Adjust based on the more restrictive dimension
            if (($max_width / $max_height) > $aspect_ratio) {
                $max_width = (int) round($max_height * $aspect_ratio);
            } else {
                $max_height = (int) round($max_width / $aspect_ratio);
            }
        } elseif ($max_width) {
            $max_height = (int) round($max_width / $aspect_ratio);
        } elseif ($max_height) {
            $max_width = (int) round($max_height * $aspect_ratio);
        } else {
            $max_width = $original_width;
            $max_height = $original_height;
        }

        return [$max_width, $max_height];
    }

    /**
     * Prepare image for transparency support if applicable.
     *
     * @param \GdImage $gdImage The original GD image.
     * @param \GdImage $resizedImage The destination GD image.
     * @param array $upload The upload data.
     * 
     * @return void
     */
    private function prepareImageForTransparency($gdImage, $resizedImage, $upload)
    {
        $imageExtension = pathinfo($upload['fullPath'], PATHINFO_EXTENSION);

        if ($imageExtension === 'png') {
            $sourceHasTransparency = $this->isImageTransparent($gdImage);

            imagesavealpha($resizedImage, true);

            if (!$sourceHasTransparency) {
                $background_color = $this->getBackgroundColor($gdImage);
                $background_color = imagecolorallocate($resizedImage, $background_color[0], $background_color[1], $background_color[2]);
                imagefill($resizedImage, 0, 0, $background_color);
            } else {
                $transparent_color = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                imagefill($resizedImage, 0, 0, $transparent_color);
            }
        }
    }
}
