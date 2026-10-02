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
     * @param bool $equalResize Force exact target dimensions (center-cropped cover) if true.
     * @return bool
     */
    public function resize($width = null, $height = null, $equalResize = false)
    {
        $this->loop(function ($response) use ($width, $height, $equalResize) {
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

                // Calculate crop and target positioning based on equalResize setting
                [$src_x, $src_y, $src_w, $src_h, $new_width, $new_height] = $this->calculateDimensions(
                    [$source_width, $source_height],
                    $width,
                    $height,
                    $equalResize
                );

                // Create a new image with the destination dimensions
                $resizedImage = @imagecreatetruecolor($new_width, $new_height);

                // Check if the destination image resource was created successfully
                if ($resizedImage) {
                    $this->prepareImageForTransparency($gdImage, $resizedImage, $upload);

                    // Perform the image resizing/cropping operation
                    imagecopyresampled(
                        $resizedImage,
                        $gdImage,
                        0,
                        0,
                        $src_x,
                        $src_y,
                        $new_width,
                        $new_height,
                        $src_w,
                        $src_h
                    );
                }
                
                // Save a copy of the resized image
                $this->saveImage(
                    $resizedImage, 
                    $upload['fullPath'], 
                    60
                );

                $this->freeMemoryFromImage($gdImage);

                // Run bucket method for further processing
                $this->bucket($upload);
            }
        });
    }

    /**
     * Calculate coordinates and dimensions based on aspect ratio or exact crop (equalResize).
     *
     * @param array $imageSource [width, height]
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @param bool $equalResize
     * @return array [$src_x, $src_y, $src_w, $src_h, $new_width, $new_height]
     */
    private function calculateDimensions(array $imageSource, $maxWidth, $maxHeight, bool $equalResize = false): array
    {
        [$original_width, $original_height] = $imageSource;

        if (!$maxWidth && !$maxHeight) {
            return [0, 0, $original_width, $original_height, $original_width, $original_height];
        }

        // Standard aspect ratio calculation (proportional fit inside bounds)
        if (!$equalResize || !$maxWidth || !$maxHeight) {
            [$new_width, $new_height] = $this->imageAspectRatio($imageSource, $maxWidth, $maxHeight);
            return [0, 0, $original_width, $original_height, $new_width, $new_height];
        }

        // Equal Resize (Force exact $maxWidth x $maxHeight by center-cropping original)
        $target_width = (int) $maxWidth;
        $target_height = (int) $maxHeight;

        $src_aspect = $original_width / $original_height;
        $target_aspect = $target_width / $target_height;

        if ($src_aspect > $target_aspect) {
            // Source is wider: crop sides
            $src_w = (int) round($original_height * $target_aspect);
            $src_h = $original_height;
            $src_x = (int) round(($original_width - $src_w) / 2);
            $src_y = 0;
        } else {
            // Source is taller: crop top & bottom
            $src_w = $original_width;
            $src_h = (int) round($original_width / $target_aspect);
            $src_x = 0;
            $src_y = (int) round(($original_height - $src_h) / 2);
        }

        return [$src_x, $src_y, $src_w, $src_h, $target_width, $target_height];
    }

    /**
     * Automatically adjusts image size to fit into the provided dimensions maintaining ratio.
     *
     * @param array $imageSource
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @return array
     */
    private function imageAspectRatio(array $imageSource, $maxWidth, $maxHeight): array
    {
        [$original_width, $original_height] = $imageSource;
        $aspect_ratio = $original_width / $original_height;

        if ($maxWidth && $maxHeight) {
            $width_ratio  = $maxWidth / $original_width;
            $height_ratio = $maxHeight / $original_height;
            $scale = min($width_ratio, $height_ratio);

            $maxWidth  = (int) round($original_width * $scale);
            $maxHeight = (int) round($original_height * $scale);
        } elseif ($maxWidth) {
            $maxHeight = (int) round($maxWidth / $aspect_ratio);
        } elseif ($maxHeight) {
            $maxWidth = (int) round($maxHeight * $aspect_ratio);
        } else {
            $maxWidth = $original_width;
            $maxHeight = $original_height;
        }

        return [$maxWidth, $maxHeight];
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
        $imageExtension = strtolower(pathinfo($upload['fullPath'], PATHINFO_EXTENSION));

        if (in_array($imageExtension, ['png', 'webp'], true)) {
            $sourceHasTransparency = $this->isImageTransparent($gdImage);

            imagealphablending($resizedImage, false);
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