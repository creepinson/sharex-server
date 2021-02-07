# PHP Sharex Server
A php sharex server (WIP)

## Installation
Clone this repo in a static web-server folder (eg. /var/www/html/cdn) and change the config.yaml api keys array to the api keys you want to use.

Install the dependencies with [composer](https://getcomposer.org/).

```bash
composer update
```

Next, create the uploads/ folder within the repository root. This is where uploaded files will be stored:

```bash
mkdir uploads
```

### Web Server Config

You will need an instance of the NGINX web server installed, along with php-fpm (I'm using php7.4-fpm in this case) installed on your system. 

Next, create a nginx configuration file like so in `/var/www/nginx/sites-available`.

```nginx
server {
    listen 80;
    # Your CDN Domain
    server_name dev.localhost;
    # The location of this repository that was git-cloned. 
    root /home/theo/Documents/sharex-server;

    index index.html index.htm index.php;

    location / {
        try_files $uri $uri/ $uri.php =404;
    }

    # This makes /embed/test.png work for use in social media & Discord.
    rewrite ^/embed(.*)\.(jpe?g|png|gif|ico|bmp|json|yaml|gif|mp4)$ /index.php?img=$1.$2 redirect;

    location ~ \.php$ {
        include fastcgi.conf;
        # PHP Location
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
    }
}
```

You'll need to replace the root and server_name variables with their correct values.

To enable this new config file, run the following command in your terminal, replacing example.conf with your config file name.

```bash
sudo ln -s /etc/nginx/sites-available/example.conf /etc/nginx/sites-enabled/
```

Now you can enable php-fpm and nginx.

```bash
sudo systemctl enable --now php7.4-fpm nginx
```

## ShareX Config

You can use `https://example.com/upload.php?key=1234` as the URL in the ShareX Custom Uploaders section.
Obviously you'll need to change the domain and the api key here.

Screenshot example coming soon.

### Sharenix (LINUX VERSION)
You can also edit the .sharenix.json file for sharenix.
Obviously you'll need to change the domain and the api key here.

## Credits
[Original credit goes to a guy who made it for flameshot](https://gist.github.com/seamus-45/3126a181e95ad0265f9d48ad89b58cfc).
