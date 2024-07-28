<?php

use Tamedevelopers\File\File;


// all available mime types
// default is images

File::name('avatar')
    ->mime('image');


File::name('document')
    ->mime('file');





// Extension Type
$extensionType = [
    'video'         =>  ['.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
    'audio'         =>  ['.mp3', '.wav'],
    'file'          =>  ['.docx', '.doc', '.pdf', '.txt'],
    'image'         =>  ['.jpg', '.jpeg', '.png', '.gif'],
    'zip'           =>  ['.zip', '.rar'],
    'pdf'           =>  ['.pdf'],
    'xls'           =>  ['.xlsx', '.xls'],
    'doc'           =>  ['.docx', '.doc', '.txt'],
    'general_image' =>  ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.ico'],
    'general_media' =>  ['.mp3', '.wav', '.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
    'general_file'  =>  ['.docx', '.doc', '.pdf', '.txt', '.zip', '.rar', '.xlsx', '.xls'],
];