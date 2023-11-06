Billing With Laravel Cashier

1-Video (Installation and Usage)

install command
composer require laravel/cashier

php artisan migrate

add this trait in User model
use Billable;

make aacount in stripe and get these keys
Set apis key in env

make two prducts in stripe dashboard
