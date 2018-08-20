# psst
Single-script file sharing system in PHP  
Current version: `1.3`  

## Requirements
1. PHP 5+
2. [secSesh](https://github.com/rahuldottech/secSesh) for secure session handling. Save `secsesh.php` in the same folder as `psst.php`, thats it!

## Installation
1. Save `psst.php` on your server. You can rename it.  
2. Create a directory where your files will be uploaded. Make sure it is publicly accessibly.  
3. Configure the options at the top of `psst.php`.  
4. Save [`secsesh.php`](https://github.com/rahuldottech/secSesh) in the same folder as `psst.php`, or if you already have it on your server, change the path of the file in the script.
4. All done!  

## Features
1. File size limit  
2. File extention allowances  
3. File extention exclusions  
4. HTTPS enforce through redirects  
5. Password protection for uploading interface  
6. Super simple (and mobile-friendly) UI  
7. Completely valid HTML
8. No JavaScript required
9. Single-file script

## Misc. Considerations
1. You might want to enforce SSL through your server options, because the setting in the file will only enforce it for the script itself and won't (can't) enforce it for the files you share.  
2. You also might want to leave an empty `index.html` file in the directory where your files are stored, or diable directory listing

## Report Bugs
Create an Issue or tweet to me at @rahuldottech


