<?php

use Tamedevelopers\File\File;
use Tamedevelopers\Support\Env;


//on using comoposer autoload
include_once __DIR__  . "/../vendor/autoload.php";

// Env::loadOrFail();

config_file(
    config: [
        'size' => '3mb',
        'baseDir' => '/' // root directory
    ],
    class: [
        'error'   => 'background: #f7b9b9; margin: 0 auto 50px; width: 100%; max-width: 600px; padding: 20px; font-size: 18px',
        'success' => 'background: #c3f9c3; margin: 0 auto 50px; width: 100%; max-width: 600px; padding: 20px; font-size: 18px',
    ]
);

$upload = File::name('avatar')
            ->folder('upload')
            ->generate(false)
            ->size('10.5mb') // override global settings
            ->mime('image')
            ->validate()
            ->save(function($response){

                // dd(
                //     $response->getConfig(),
                //     $response->getError(),
                //     $response->get(),
                //     tasset("public/{$response->first('path')}", true),
                // );

                $response
                ->watermark('tests/watermark.png', 'top right', 20)
                    ->resize(400, 400)
                    ->compress()
                    ;
            });

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
            
                <h3 class="valign-wrapper prod_hding_main mb-3">Upload file</h3>

                <div style="<?= $upload->getClass(); ?>">

                    <?php if($upload->hasError()) {?>
                        <?= $upload->getMessage(); ?>
                    <?php } elseif($upload->isCompleted())  {?>
                        <a href="<?= $upload->first('url'); ?>" target="_blank">
                            Preview Data
                        </a>
                    <?php } ?>
                    
                </div>
                
                <!--file upload-->
                <div class="col-sm-12 mt-3">
                    <div class="form-group">
                        <label for="upload">Image</label>
                        <input type="file" class="form-control-file" id="upload" 
                                name="avatar[]" multiple>
                    </div>
                </div>

                <button type="submit" style="margin-top: 40px;">
                    Upload File
                </button>
            
        </form>
    </center>
</body>
</html>