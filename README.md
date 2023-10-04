# File Upload

[![Total Downloads](https://poser.pugx.org/tamedeveloper/file/downloads)](https://packagist.org/packages/tamedeveloper/file)
[![Latest Stable Version](https://poser.pugx.org/tamedeveloper/file/version.png)](https://packagist.org/packages/tamedeveloper/file)
[![License](https://poser.pugx.org/tamedeveloper/file/license)](https://packagist.org/packages/tamedeveloper/file)
[![Build Status](https://github.com/tamedevelopers/file/actions/workflows/php.yml/badge.svg)](https://github.com/tamedevelopers/file/actions)
[![Code Coverage](https://codecov.io/gh/tamedeveloper/file/branch/2.2.x/graph/badge.svg)](https://codecov.io/gh/tamedeveloper/file/branch/3.2.2.x)

## Documentation

* [Requirements](#requirements)
* [Installation](#installation)
* [Instantiate](#instantiate)
* [Global Config](#global-config)
* [Error Status Code](#error-status-code)
    * [Get Message](#get-message)
    * [Get Status](#get-status)
* [Usage](#usage)
  * [INPUT HTML STRUCTURE](#input-html-structure)
  * [Driver](#driver)
  * [Name](#name)
  * [Folder](#folder)
  * [Structure](#structure)
  * [Size](#size)
  * [Limit](#limit)
  * [Mime](#mime)
  * [Width](#width)
  * [Height](#height)
  * [Validate](#validate)
  * [Save](#save)
  * [Resize](#resize)
  * [WaterMark](#watermark)
  * [Compress](#compress)
  * [First](#first)
  * [Get](#get)
  * [Get Image Size](#get-image-size)
  * [Get Mime Type](#get-mime-type)
  * [Not Empty](#not-empty)
  * [Is Empty](#is-empty)
  * [Has Error](#has-error)
  * [Mime Types](#mime-types)
* [Useful links](#useful-links)


## Requirements

- `>= php 8.0+`

## Installation

Prior to installing `support package` get the [Composer](https://getcomposer.org) dependency manager for PHP because it'll simplify installation.

```
composer require tamedevelopers/file
```

## Instantiate

**Step 1** — Composer  `Instantiate class using`:

```
require_once __DIR__ . '/vendor/autoload.php';

use \Tamedevelopers\File\File;

$file = new File();
```

- **Example 2**
```
require_once __DIR__ . '/vendor/autoload.php';

$file = new Tamedevelopers\File\File();
```

- or -- `Helpers Function`
```
$file = TameFile();
```

## Global Config
- Configure The Global Config, so you don't have to always include default settings
    - Define as `named argument`


| Keys          |  Types            |      Description                                  |
|---------------|-------------------|---------------------------------------------------|
| message       |  Assoc `array`    | Create all error message in different language    |
| config        |  Assoc `array`    | Create all needed config data                     |
| filterError   |  Index `array`    | Remove `error status` you do not want to validate |

```
FileConfig(
    message: [
        '401'   => 'Select file to upload',
        '402'   => 'File upload is greater than allowed size of:',
        '403'   => 'Maximum file upload exceeded. Limit is:',
        '404'   => 'Uploaded file format not allowed. Allowed formats:',
        '405'   => 'Image dimension allowed is:',
        '405x'  => 'Image dimension should be greater than or equal to:',
        '200'   => 'File uploaded successfully:',
        'kb'    => 'kb',
        'mb'    => 'mb',
        'gb'    => 'gb',
        'and'   => 'and',
        'width' => 'width',
        'height'=> 'height',
        'files' => 'files',
        'file'  => 'file',
    ],
    config: [
        'limit'         => 1,
        'mime'          => 'images', // video|audio|files|images|general_image|general_media|general_file
        'size'          => 2097152, // 2mb
        'baseUrl'       => domain(),
        'baseDir'       => base_path(),
        'driver'        => 'local',
        'structure'     => 'default', // default|year|month|day
        'generate'      => true, // will always generate a unique() name for each uploaded file
    ],
    filterError: []
);
```

## Error Status Code
- All Error Status Code

| Status Code   |   Description                                         |
|---------------|-------------------------------------------------------|
| 401           |   Select file to upload                               |
| 402           |   File upload is greater than allowed size of   |
| 403           |   Maximum file upload exceeded. Limit is              |
| 404           |   Uploaded file format not allowed. Allowed formats   |
| 405           |   Image dimension allowed is                          |
| 200           |   File uploaded successfully                          |

### Get Message
```
$file = File::name('html_input_name');

$file->getMessage();
```

### Get Status
```
$file = File::name('html_input_name');

$file->getStatus();
```


## Usage

### INPUT HTML STRUCTURE

```
<input type="file" name="avatar">
```
- or -- `For Multiple Data`

```
<input type="file" name="avatar[]" multiple>
```

### Driver
- More drivers are to be added in the future
    - By default driver is set to `local`

| Type      |   Description                                                                                         |
|-----------|-------------------------------------------------------------------------------------------------------|
| s3        |   Amazon `s3` driver. In other to use `s3`, you'll need to install `composer require aws/aws-sdk-php` |
| local     |   Local `host\|server` driver.                                                                        |


### Name
- Takes one param `string` as input name
    - Static method by default

```
File::name('html_input_name');
```

### Folder
- Takes one param `string` as `folder_path` to save file
    - Remember the system already have your `baseDirectory`


```
File::name('avatar')
    ->folder('upload/user');
```

### Structure
- Takes one param `string` as `structure type`
    - Best used for `Media\|Blog\|Newsletter` Websites.

| Type      |   Description                                                         |
|-----------|-----------------------------------------------------------------------|
| default   |   Files will be uploaded in defaut folder path                        |
| year      |   Files will be uploaded in `default/year` path: `folder/2023`        |
| month     |   Files will be uploaded in `default/year` path: `folder/2023/02`     |
| day       |   Files will be uploaded in `default/year` path: `folder/2023/02/30`  |


1. Default
    - Year
        - Month
            - Day

```
File::name('avatar')
    ->structure('month');
```

### Size
- Takes one param `string|int`
    - size in `int` or `kb/|mb/|gb`

```
File::name('avatar')
    ->size('1.5mb'); // will be converted to:  1.5 * (1024 * 1024) = 1572864

or
File::name('avatar')
    ->size(2097152); // = 2097152|2mb
```

### Limit
- Takes one param `string|int`
    - Default limit is set to `1` upload

```
File::name('avatar')
    ->limit(2);
```

### Mime
- Takes one param `string` as mime type
    - The package already create list of mim types

| key               |   Description                                                                                                     |
|-------------------|-------------------------------------------------------------------------------------------------------------------|
| video             |   `['video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/x-ms-wmv']`                               |
| audio             |   `['audio/mpeg','audio/x-wav']   `                                                                               |
| files             |   `['application/msword','application/pdf','text/plain']   `                                                      |
| images            |   `['image/jpeg', 'image/png', 'image/gif']`                                                                      |
| general_image     |   `['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/vnd.microsoft.icon']`                            |
| general_media     |   `['audio/mpeg','audio/x-wav', 'video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/x-ms-wmv']`   |
| general_file      |   [
                            'application/msword','application/pdf','text/plain','application/zip', 'application/x-zip-compressed', 'multipart/x-zip',
                            'application/x-zip-compressed', 'application/x-rar-compressed', 'application/octet-stream', 
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ]                                                                                                              |


- Usage
```
File::name('avatar')
    ->mime('images');
```

### Width
- Takes two param `string\|int` and `bool`
    - 1st param is `string\|int`. width size
    - 2nd param is `bool`. This allow to check if size should be === or >= size of uploaded image. Default is `true`

```
File::name('avatar')
    ->width(700, false);
```

### Height
- Same as `width` method

```
File::name('avatar')
    ->width(700)
    ->height(400);
```

### Validate
- Takes an [optional] param as a `callable\|closure` function.
    - The method needs to be called to validate upload errors before saving

```
File::name('banner')
    ->folder('upload/banner')
    ->width(700)
    ->height(400)
    ->validate();
```
- or -- `passing a closure`
```
File::name('banner')
    ->folder('upload/banner')
    ->validate(function($response){

        // perform any other needed task in here
    });
```


### Save
- Takes an [mandatory] param as a `callable\|closure` function.
    - Calling this [method] will automatically save uploaded data on `success`.

```
File::name('banner')
    ->folder('upload/banner')
    ->validate()
    ->save(function($response){

        // perform any other needed task in here
    });
```

### Resize
- Takes two param as `size` `int` width and height
    - Returns an instance of self

```
File::name()
    ->folder('upload/banner')
    ->validate('avatar')
    ->save(function($response){

        // perform resize
        // width, height
        $response->resize(400, 400);
    });
```

### WaterMark
- Returns an instance of self

```
File::name('avatar')
    ->folder('upload/banner')
    ->validate()
    ->save(function($response){

        // perform watermark
        $response->watermark('watermark.png', 'center');
    });
```

### Compress
- Returns an instance of self

```
File::name('avatar')
    ->folder('upload/banner')
    ->validate()
    ->save(function($response){

        // perform compressor
        $response->compress();

        // you can perform method chaining as well
        $response->resize(200, 450)
                    ->watermark('watermark.png', 'center')
                    ->compress();
        
    });
```

### First
- This will get the first uploaded data 

```
->save(function($response){

    $response->first();
    

    returns as an array  [name, url]
});
```

### Get
- This will get all uploaded data 

```
->save(function($response){

    $response->get();
});

or

$upload = File::name('avatar')
            ->validate()
            ->save();


$upload->first();
```

### Get Image Size
- Takes one param as `string`
    - Return an `array\|null`

```
File::getImageSize('full_source_path')

[
  ["height"]    => int(4209)
  ["width"]     => int(3368)
]
```

### Get Mime Type
- Takes one param as `string`
    - Return `string\|bool`. `false` on error.

```
File::getMimeType('full_source_path')
```

### Not Empty
- Takes one param as `string`. Input file name
    - Return bool `true\|false`

```
File::notEmpty('avatar');
File::isNotEmpty('avatar');
File::has('avatar');
```

### Is Empty
- Same as not empty

```
File::isEmpty('avatar')
```

### Has Error
- Returns true or false. Check if there's an error in the upload

```
$file = File::name('avatar')

if($file->hasError()){

}
```

### Mime Types
```
'video'         =>  ['.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
'audio'         =>  ['.mp3', '.wav'],
'files'         =>  ['.docx', '.pdf', '.txt'],
'images'        =>  ['.jpg', '.jpeg', '.png'],
'general_file'  =>  ['.docx', '.pdf', '.txt', '.zip', '.rar', '.xlsx', '.xls'],
'general_image' =>  ['.jpg', '.jpeg', '.png', '.webp'],
'general_media' =>  ['.mp3', '.wav', '.mp4', '.mpeg', '.mov', '.avi', '.wmv']

Pass in any of this into the Type parameter section when calling the ->run Method
```
- video
- audio
- files
- general_file
- images 
- general_image
- general_media


## Useful Links

- @author Fredrick Peterson (Tame Developers)
- If you love this PHP Library, you can [Buy Tame Developers a coffee](https://www.buymeacoffee.com/tamedevelopers)
