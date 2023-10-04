<?php

use Tamedevelopers\File\File;
use Tamedevelopers\Support\Env;
use Tamedevelopers\File\AmazonS3;
use Tamedevelopers\Support\Server;

//on using comoposer autoload
include_once __DIR__  . "/../vendor/autoload.php";

Env::loadOrFail();


FileConfig(
    config: [
        'size' => '3mb'
    ],
);

// easy as this
File::name('avatar')
    ->validate()
    ->save();


// or more methods chaining
File::name('avatar2')
    ->limit(3)
    ->mime('files')
    // ->driver('s3')
    ->folder('public/images')
    ->validate()
    ->save();


// closure/callable on save() and validate()
$upload = File::name('banners')
            ->size('50kb')
            ->filterError([401])
            ->validate()
            ->save(function($response){

                $response
                    ->watermark('test/watermark.png', 'bottom-left', 50)
                    ->resize(690, 540)
                    ->compress();
            });


// has method
if(File::has('avatar')){
    // perform query
}


// 
if(File::has('avatar')){

    $file = File::name('avatar')
        ->validate()
        // ->driver('s3')
        ->save();

    dump(
        $file->getMessage(),
        $file->getStatus(),
        $file->first(),
    );
}
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>
</head>
<body>
<center>
    <form method="post" enctype="multipart/form-data">
        
        <h3 class="valign-wrapper prod_hding_main" style="margin: 0 0 50px;">
            Upload file
        </h3>

        <?php if($upload->hasError()) {?>
            <div style="background: #f7b9b9; margin: 0 auto 50px; width: 100%; max-width: 600px; padding: 20px; font-size: 18px">
                <?= $upload->getMessage(); ?>
            </div>
        <?php } ?>

        
        <!--file upload-->
        <div class="col-sm-12 mt-3" style="margin: 0 0 30px;">
            <div class="form-group">
                <label for="upload">Image Avatar</label>
                <input type="file" class="form-control-file" id="upload" 
                        name="avatar" multiple>
            </div>
        </div>
        
        <!--file upload-->
        <div class="col-sm-12 mt-3">
            <div class="form-group">
                <label for="upload">Banners</label>
                <input type="file" class="form-control-file" id="upload" 
                        name="banners[]" multiple>
            </div>
        </div>

        <button type="submit" style="margin-top: 40px;">
            Upload File
        </button>
        
    </form>
</center>
</body>
</html>

