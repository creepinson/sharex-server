# PHP Sharex Server
A php sharex server (WIP)

## Installation
Place this repo in a static php-compatible web server and change the config.json api keys array to the api keys you want to use. The following steps are optional:
- You can edit the upload.php file and change the file upload size, the allowed file extensions, and more. 
- You can also edit the .sharenix.json file for sharenix, or you can use `https://example.com/upload.php?key=1234` with sharex. Obviously you'll need to change the domain and the api key here.
- If you do not want to store the files in ./files you can change the `$basePath` variable.
Also, **make sure you have a `files` direcctory in your web root so the script can to upload to it.**


## Credits
[Original credit goes to a guy who made it for flameshot](https://gist.github.com/seamus-45/3126a181e95ad0265f9d48ad89b58cfc).
