<?php

declare(strict_types=1);

namespace Tamedevelopers\File;

use Aws\S3\S3Client;
use Tamedevelopers\Support\Env;
use Aws\S3\Exception\S3Exception;


class AmazonS3
{    
    /**
     * s3
     *
     * @var mixed
     */
    static private $s3;

    /**
     * bucketName
     *
     * @var mixed
     */
    static private $data;

    /**
     * Check if data has been initialized
     *
     * @var bool
     */
    static private $initialize;


    /**
     * Config Bucket Instance
     *
     * @param  mixed $accessKey
     * @param  mixed $secretKey
     * @param  mixed $bucketName
     * @return $this
     */
    static public function config($accessKey = null, $secretKey = null, $bucketName = null, $region = null, $endpoint = null)
    {
        // config
        [$accessKey, $secretKey, $bucketName, $region, $endpoint] = self::getConfigData($accessKey, $secretKey, $bucketName, $region, $endpoint);

        // data compile
        self::$data = [
            'accessKey' => $accessKey,
            'secretKey' => $secretKey,
            'bucketName' => $bucketName,
            'region' => $region,
            'endpoint' => $endpoint,
        ];

        self::$s3 = new S3Client([
            'version' => 'latest',
            'region'  => self::$data['region'], 
            'credentials' => [
                'key'    => self::$data['accessKey'],
                'secret' => self::$data['secretKey'],
            ],
        ]);

        self::$initialize = true;

        return new static();
    }

    /**
     * Save Upload File
     *
     * @param  mixed $sourcePath
     * @param  mixed $key
     * @return mixed
     */
    static public function save($sourcePath, $key)
    {
        self::isInitialize();
        
        try {
            $result = self::$s3->putObject([
                'Bucket'        => self::$data['bucketName'],
                'Key'           => $key,
                'SourceFile'    => $sourcePath,
                'ContentType'   => mime_content_type($sourcePath), 
                'Metadata'      => [
                    'Content-Disposition' => 'attachment',  // Set the Content-Disposition header
                ],
            ]);

            return $result->get('ObjectURL');
        } catch (S3Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Update Upload File
     *
     * @param  mixed $sourcePath
     * @param  mixed $key
     * @return bool
     */
    static public function update($sourcePath, $key)
    {
        return self::save($sourcePath, $key);
    }

    /**
     * Get from s3 bucket
     *
     * @param string|null $key
     * @param string|null $default
     * @return string
     */
    static public function get($key = null, ?string $default = 'null')
    {
        $awsUrl = env('AWS_URL');
        if(!empty($awsUrl)){
            return  rtrim($awsUrl, '\/') . '/' . $key;
        }

        self::isInitialize();
        
        try {
            // Get the public URL of the object
            return self::$s3->getObjectUrl(self::$data['bucketName'], $key ?? $default);
        } catch (S3Exception $e) {
            return $default;
        }
    }

    /**
     * Delete from s3 bucket
     *
     * @param string|null $key
     * @return void
     */
    static public function delete($key = null)
    {
        self::isInitialize();

        // return false if key is empty
        if(empty($key)){
            return false;
        }

        try {
            self::$s3->deleteObject([
                'Bucket' => self::$data['bucketName'],
                'Key'    => $key,
            ]);
        
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }
    
    /**
     * isInitialize
     *
     * @return void
     */
    static private function isInitialize()
    {
        if(!self::$initialize){
            self::config();
        }
    }
    
    /**
     * Get Configuration Data
     *
     * @param  mixed $accessKey
     * @param  mixed $secretKey
     * @param  mixed $bucketName
     * @param  mixed $region
     * @param  mixed $endpoint
     * @return array
     */
    static private function getConfigData($accessKey = null, $secretKey = null, $bucketName = null, $region = null, $endpoint = null)
    {
        return [
            Env::env('AWS_ACCESS_KEY_ID', $accessKey),
            Env::env('AWS_SECRET_ACCESS_KEY', $secretKey),
            Env::env('AWS_BUCKET', $bucketName),
            Env::env('AWS_DEFAULT_REGION', $region ?? 'us-east-1'),
            Env::env('AWS_USE_PATH_STYLE_ENDPOINT', $endpoint ?? false),
        ];
    }

}
