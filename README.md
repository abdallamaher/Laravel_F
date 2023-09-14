## Installation

1. Install laravel sail https://laravel.com/docs/8.x/sail
1. Clone this Repository
1. Copy .env.example to .env
1. Create a database and update the .env file with the database credentials
1. Run "./vendor/bin/sail up -d"
1. Run "./vendor/bin/sail artisan key:generate"
1. Run "./vendor/bin/sail artisan migrate"
1. Run "./vendor/bin/sail artisan db:seed"
