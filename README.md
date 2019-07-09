# SecretBox

## Config SSL on nginx web server
- Install nginx
- Use OpenSSL to generate all of our certificates
- Copy nginx.config to /etc/nginx/sites-enable/default for Ubuntu
- Rename localhost to secretbox.io
- Add your self-signed certificate to your browser

## Run the back-end server
```
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

## Run the front-end server
```
npm install
npm run serve
```