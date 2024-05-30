# Blog admin panel

This project is simple admin panel for a blog, it made for learning purposes using 
Laravel and Filament.

I've decided not to mix admin side and actual frontend side, so this one is just for administration part.

![Panel view](./blog-admin.png)

## Steps to run it
 - run `composer install`
 - run `npm install`
 - set up your `.env` with access to database
 - `APP_SHARED_STORAGE` used for attachments and thumbnails storage (to share with frontend)
 - link your storage (`make storage`)
 - run migrations with (`php artisan migrate`)
 - you may need to clear your caches or cache views/routes, run command `make cc`
 - create new user with `php artisan make:filament-user`

## Nginx config
In case you want to run it with nginx I'll share my local config:
```nginx
server {
    listen 80;
    server_name blog-admin.ineersa.local www.blog-admin.ineersa.local;
    root /var/www/blog-admin/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
     }

    location ~ /\.ht {
        deny all;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    error_log /var/log/nginx/blog-admin.ineersa.local.error.log;
    access_log /var/log/nginx/blog-admin.ineersa.local.access.log;
}

```
