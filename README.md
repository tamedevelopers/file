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
* [Amazon Aws S3](#amazon-aws-s3)
* [Global Config](#global-config)
* [Response Data](#response-data)
    * [Get Message](#get-message)
    * [Get Status](#get-status)
    * [Get Class](#get-class)
    * [First](#first)
    * [Get](#get)
    * [Form](#form)
    * [Unlink](#unlink)
* [Usage](#usage)
  * [INPUT HTML STRUCTURE](#input-html-structure)
  * [Name](#name)
  * [Driver](#driver)
  * [BaseDir](#baseDir)
  * [Generate](#generate)
  * [Folder](#folder)
  * [Filter](#filter)
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
  * [Get Image Size](#get-image-size)
  * [Get Mime Type](#get-mime-type)
  * [Not Empty](#not-empty)
  * [Is Empty](#is-empty)
  * [Has Error](#has-error)
  * [Is Completed](#is-completed)
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

use Tamedevelopers\File\File;

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

## Amazon Aws S3
- To use s3 first require `composer require aws/aws-sdk-php`
- By default Aws load entire SDK we won't be needing
    - Copy the below code into your root `composer.json` and then run `composer update` in terminal.

```
"scripts": {
    "pre-autoload-dump": "Aws\\Script\\Composer\\Composer::removeUnusedServices"
},
"extra": {
    "aws/aws-sdk-php": [
        "Ec2",
        "CloudWatch"
    ]
}
```

## Global Config
- Configure The Global Config, so you don't have to always include default settings
    - Define as `named argument`


| Key           |  Types            |      Description                  |
|---------------|-------------------|-----------------------------------|
| message       |  Assoc `array`    | Create all error messages         |
| config        |  Assoc `array`    | Create all needed config data     |
| class         |  Assoc `array`    | Create error and success class    |

```config
config_file(
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
        'mime'          => 'image', // video|audio|file|image|general_image|general_media|general_file
        'size'          => 2097152, // 2mb
        'baseDir'       => 'public',
        'driver'        => 'local',
        'structure'     => 'default', // default|year|month|day
        'generate'      => true, // will always generate a unique() name for each uploaded file
    ],
    class: [
        'error'     => 'bg-danger',
        'success'   => 'bg-success',
    ]
);
```

- or --  using file class method `Global Config`
```
(new File)->globalConfig(
    message: [], 
    config: [],
    class: []
);
```

## Response Data
- How to retrieve data

### Get Message
- This will return error message
```
$file = File::name('html_input_name');

$file->getMessage();
```

### Get Status
- This will return error status code
```
$file = File::name('html_input_name');

$file->getStatus();
```

### Get Class
- This will only return msg, if there's an error or success
```
$file = File::name('html_input_name');

$file->getClass();
```

### First
- This will get the first uploaded data 
    - [optional] You can pass the mode as string `name` \| `path`\|`url`

```
->save(function($response){

    $response->first();
});
```

- `or`
```
$upload = File::name('avatar')
                ->save();

$upload->first('url);
```

### Get
- This will get all uploaded data 
    - Returns an index array of all uploaded data, `[name, path, url]`
    - [optional] You can pass the mode as string `name` \| `path`\|`url`

```
->save(function($response){

    $response->get();
});
```

- `or`
```
$upload = File::name('avatar')
            ->save();

$upload->get('name);
```

### Form
- Return form request data
    - This will return `validator package` object [Validator](https://github.com/tamedevelopers/validator) 

```
->save(function($response){

    $response->form();
});
```

### Unlink
- Accepts two param as string. This method uses the base path automatically.
    - [mandatory] `$fileToUnlink` Path to file, you want to unlink.
    - [optional] `$checkFile`. This will check if `$fileToUnlink` is not same, before.

```
->save(function($response){

    $userAvatar;

    $response->unlink("public/images/{$userAvatar}", "avatar.svg");

    <!-- the above will only unlink when value is not avatar.svg -->
});
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

### Name
- Takes one param `string` as input name
    - Static method by default

```
File::name('html_input_name');
```


### Driver
- More drivers are to be added in the future
    - By default driver is set to `local`

| Key       |   Description                                                                 |
|-----------|-------------------------------------------------------------------------------|
| s3        |   Amazon `s3` driver. The package loaded `[Ec2 and CloudWatch]` needed by s3  |
| local     |   Local `host server` driver.                                                 |

```
File::name('avatar')
    ->driver('s3');
```

- `using driver if project is not in any Frameworks`
```
use Tamedevelopers\Support\Env;

// if your project is not on on core php, then you'll need to load env.
// this will create env dummy data and as well load the .env file.
// once data has been created, you can remove the `Env::createOrIgnore();`


Env::createOrIgnore();
Env::load();

File::name('avatar')
    ->driver('s3');
```

### BaseDir
- Takes one param `string` as base directory name
    - This will override the global configuration settings (Domain and Server Path will be set)

```
File::name('avatar')
    ->baseDir('newBaseDirectory');
```

### Generate
- Takes one param `bool`

```
File::name('avatar')
        ->generate(false);
```

### Folder
- Takes one param `string` as `folder_path` to save file

```
File::name('avatar')
    ->folder('upload/user');
```

### Filter
- Takes index or closed array index
    - Remove `error status code` you do not want to validate
    - You cannot remove Error `200`

| Status Code   |   Description                                         |
|---------------|-------------------------------------------------------|
| 401           |   Select file to upload                               |
| 402           |   File upload is greater than allowed size of         |
| 403           |   Maximum file upload exceeded. Limit is              |
| 404           |   Uploaded file format not allowed. Allowed formats   |
| 405           |   Image dimension allowed is                          |
| 200           |   File uploaded successfully                          |

```
File::name('avatar')
    ->filter(401, 402);
```

- `or`
```
File::name('avatar')
    ->filter([401, 402, 405]);
```

### Structure
- Takes one param `string` as `structure type`
    - Best used for `Media\|Blog\|Newsletter` Websites.

| Key       |   Description                                                         |
|-----------|-----------------------------------------------------------------------|
| default   |   Files will be uploaded in defaut folder path                        |
| year      |   Files will be uploaded in `default/year` path: `folder/yyyy`        |
| month     |   Files will be uploaded in `default/year` path: `folder/yyyy/mm`     |
| day       |   Files will be uploaded in `default/year` path: `folder/yyyy/mm/dd`  |


1. Default
    - Year
        - Month
            - Day

```
File::name('avatar')
    ->structure('month');
```

### Size
- Takes one param `string` \| `int`
    - size in `int` \| `kb` \| `mb` \| `gb`

```
File::name('avatar')
    ->size('1.5mb'); // will be converted to:  1.5 * (1024 * 1024) = 1572864
```

- `or`
```
File::name('avatar')
    ->size(2097152); // = 2097152|2mb
```

### Limit
- Takes one param `string` \| `int`
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
| general_file      |   `['application/msword','application/pdf','text/plain','application/zip', 'application/x-zip-compressed', 'multipart/x-zip','application/x-zip-compressed' 'application/x-rar-compressed', 'application/octet-stream', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']`|

```
File::name('avatar')
    ->mime('images');
```

### Width
- Takes two param `string` \| `int` and `bool`
    - 1st param is `string` \| `int`. width size
    - 2nd param is `bool`. This allow to check if size should be === or >= size of uploaded image. Default is `true`

```
$file = File::name('avatar')
        ->width(700, false);

dd(
    $file
);
```

### Height
- Same as `width` method

```
File::name('avatar')
    ->width(700)
    ->height(400);
```

### Validate
- [optional] Method can be use, to return error message.
    - Takes an [optional] param as a `callable\|closure` function.


```
File::name('banner')
    ->folder('upload/banner')
    ->validate(function($response){

        // perform any other needed task in here
        echo $response->getMessage();
        return;
    });
```

### Save
- Takes an [optional] param as a `callable\|closure` function.
    - Calling this [method] will automatically save the data.

```
File::name('banner')
    ->folder('upload/banner')
    ->save(function($response){

        // perform any other needed task in here
    });
```

- `or`
```
$file = File::name('banner')
            ->folder('upload/banner')
            ->save();

dd(
    $file->get(),
    $file->first(),
);
```

### Resize
- Takes two param as `size` `int` width and height
    - Returns an instance of self

```
File::name()
    ->folder('upload/banner')
    ->save(function($response){

        // perform resize
        // width, height
        $response->resize(400, 400);
    });
```

### WaterMark
- Takes three param `watermarkSource` \| `position` \| `padding`
    - [mandatory] $watermarkSource
    - Returns an instance of self
    - Padding is applied evenly on all position apart from `center`

| Position  key     |   Description                                 |
|-------------------|-----------------------------------------------|
| bottom-right      |  Default is `bottom-right`                    |
| bottom-left       |  apply watermart to possition `bottom-left`   |
| top-right         |  apply watermart to possition `top-right`     |
| top-left          |  apply watermart to possition `top-left`      |
| center            |  apply watermart to possition `center`        |

```
File::name('avatar')
    ->folder('upload/banner')
    ->save(function($response){

        // perform watermark
        $response->watermark('watermark.png', 'center', 50);
    });
```

### Compress
- Returns an instance of self

```
File::name('avatar')
    ->folder('upload/banner')
    ->save(function($response){

        // perform compressor
        $response->compress();

        // you can perform method chaining as well
        $response->resize(200, 450)
                    ->watermark('watermark.png', 'center')
                    ->compress();
        
    });
```

### Get Image Size
- Takes one param as `string` 
    - Return an `array` \| `null`

```
File::getImageSize('full_source_path')

[
  ["height"]    => int(4209)
  ["width"]     => int(3368)
]
```

### Get Mime Type
- Takes one param as `string`
    - Return `string` \| `bool`. `false` on error.

```
File::getMimeType('full_source_path')
```

### Not Empty
- Takes one param as `string`. Input file name
    - Return bool `true` \| `false`

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

### Is Completed
- Returns true or false. Check if upload has been completed

```
$file = File::name('avatar')

if($file->isCompleted()){

}
```

### Mime Types
```
'video'         =>  ['.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
'audio'         =>  ['.mp3', '.wav'],
'file'          =>  ['.docx', '.pdf', '.txt'],
'image'         =>  ['.jpg', '.jpeg', '.png'],
'general_file'  =>  ['.docx', '.pdf', '.txt', '.zip', '.rar', '.xlsx', '.xls'],
'general_image' =>  ['.jpg', '.jpeg', '.png', '.webp'],
'general_media' =>  ['.mp3', '.wav', '.mp4', '.mpeg', '.mov', '.avi', '.wmv']
```
- video
- audio
- file
- image
- general_file
- general_image
- general_media


## Useful Links

- @author Fredrick Peterson (Tame Developers)
- If you love this PHP Library, you can [Buy Tame Developers a coffee](https://www.buymeacoffee.com/tamedevelopers)
