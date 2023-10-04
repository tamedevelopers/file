<?php

use Tamedevelopers\File\File;
use Tamedevelopers\Support\Env;


//on using comoposer autoload
include_once __DIR__  . "/../vendor/autoload.php";

// Env::loadOrFail();

File::name('avatar')
    ->folder('upload')
    ->validate()
    ->save(function($response){

        $response
            ->watermark('test/watermark.png', 'center', 20)
            ->resize(100, 200)
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
            
                <h3 class="valign-wrapper prod_hding_main mb-3">Upload file</h3>
                
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