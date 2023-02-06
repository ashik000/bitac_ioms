# Inovace IOMS

Setup instructions
1. Create a new `.env` file
2. Copy everything from `.env.example`
3. Run `composer install`
   
   If failed by this (laravel/horizon v4.3.5 requires ext-posix) error then use this command
   
   `composer install --ignore-platform-reqs`
4. Run `php artisan key:generate`
5. Run `php artisan passport:keys`
6. Run `npm install`

Running instructions
1. Run `php artisan serve` twice on port 8000 and 8001
2. Run `npm run hot`
3. Go to `localhost:8000` on your browser.

Username : admin@inovacetech.com
Password : inovacedevice
