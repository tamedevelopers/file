<?php

use Tamedevelopers\File\File;


// all available mime types
// default is images

File::name('avatar')
    ->mime('image');


File::name('document')
    ->mime('file');







// Extension MimeType
$mimeType = [
    'video'         =>  ['video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/x-ms-wmv'],
    'audio'         =>  ['audio/mpeg','audio/x-wav'],
    'file'          =>  ['application/msword','application/pdf','text/plain'],
    'image'         =>  ['image/jpeg', 'image/png', 'image/gif'],
    'general_image' =>  ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/vnd.microsoft.icon'],
    'general_media' =>  ['audio/mpeg','audio/x-wav', 'video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/x-ms-wmv'],
    'general_file'  =>  [
        'application/msword','application/pdf','text/plain','application/zip', 'application/x-zip-compressed', 'multipart/x-zip',
        'application/x-zip-compressed', 'application/x-rar-compressed', 'application/octet-stream', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ]
];  


// Extension of mime Type
$extensionType = [
    'video'         =>  ['.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
    'audio'         =>  ['.mp3', '.wav'],
    'file'          =>  ['.docx', '.pdf', '.txt'],
    'image'         =>  ['.jpg', '.jpeg', '.png', '.gif'],
    'general_image' =>  ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.ico'],
    'general_media' =>  ['.mp3', '.wav', '.mp4', '.mpeg', '.mov', '.avi', '.wmv'],
    'general_file'  =>  ['.docx', '.pdf', '.txt', '.zip', '.rar', '.xlsx', '.xls'],
];