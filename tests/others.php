<?php 

use Tamedevelopers\File\File;
use Tamedevelopers\Support\Env;

//on using comoposer autoload
include_once __DIR__  . "/../vendor/autoload.php";


Env::loadOrFail();


// easy as this
// File::name('banners')->save();


// or helper function
// TameFile('banners')->save();



// has method
if(File::has('avatar')){

    // $file = File::name('avatar')
    //             ->limit(3)
    //             ->mime('files')
    //             ->filter([401, 403, 500])
    //             ->folder('public/files')
    //             ->driver('s3')
    //             ->save();

    // dd(
    //     $file->getMessage(),
    //     $file->getStatus(),
    //     $file->first(),
    // );
}