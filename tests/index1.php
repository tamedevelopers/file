<?php

use Tamedevelopers\File\File;

//on using comoposer autoload
include_once __DIR__  . "/../vendor/autoload.php";


config_file(
    config: [
        'size' => '3mb'
    ],
    class: [
        'error'   => 'background: #f7b9b9; margin: 0 auto 50px; width: 100%; max-width: 600px; padding: 20px; font-size: 18px',
        'success' => 'background: #c3f9c3; margin: 0 auto 50px; width: 100%; max-width: 600px; padding: 20px; font-size: 18px',
    ]
);


// [optional] closure/callable function, 
// When using `save()` and `validate()` method.
$upload = File::name('avatar')
                // ->folder('upload/transaction')
                ->size('500kb')
                ->generate(false)
                ->filterExtention(['png'])
                ->save(function($response){

                    $response
                        ->resize(1000, 1200)
                        ->watermark('tests/watermark.png', 'bottom-right')
                        ->compress();
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
            
            <h3 class="valign-wrapper prod_hding_main" style="margin: 0 0 50px;">
                Upload file
            </h3>

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
            <div class="col-sm-12 mt-3" style="margin: 0 0 30px;">
                <div class="form-group">
                    <label for="upload">Image Avatar</label>
                    <input type="file" class="form-control-file" id="upload" 
                            name="avatar">
                </div>
            </div>
            
            <div>
                <label for="html">Age</label>
                <input type="text" name="age" value="<?= old('age'); ?>">
            </div>

            <button type="submit" style="margin-top: 40px;">
                Submit Data
            </button>
            
        </form>
    </center>
</body>
</html>

