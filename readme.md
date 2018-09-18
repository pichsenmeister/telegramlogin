## TelegramLogin

[TelegramLogin.com](https://telegramlogin.com) is a webservice combined with a Telegram Bot to bring social login to the Telegram platform.

TelegramLogin is based on the Laravel Framework. To get it running on your server, check the [Laravel docs](https://laravel.com/docs/5.1) to get started. As database I use MySQL, but any other storage can be used as well.

1. Checkout this repository 
 ``` git clone https://github.com/3x14159265/telegramlogin.git ```
2. Install [composer](https://getcomposer.org/doc/00-intro.md)
3. Run ``` composer install``` in telegramlogin folder
4. Copy the ```.env.example``` environment file to ```.env```
5. Replace all ```<  >``` values in ```.env``` file to your personal values
6. Set the webhook of your TelegramBot according to your ```WEBHOOK_TOKEN``` in your ```.env``` file
(if your domain is ```https://example.com``` and your ```WEBHOOK_TOKEN``` is e.g. `randomToken`, you must set the webhook of your Telegram Bot to ```https://example.com/receive/randomToken```)
7. [enable Laravel scheduler](https://laravel.com/docs/5.1/scheduling)
8. Edit  ```database/seeds/UserTableSeeder.php``` with your Telegram user data 
9. Run Laravel migration ```php artisan migrate --seed```

## Official Documentation

Documentation for the TelegramLogin can be found on a subpage of this project.
1. Run local php server ```php artisan serve```
2. Go to [http://localhost:8000/docs](http://localhost:8000/docs)

## FAQs

Check our FAQs on [messengersbox.com](http://messengersbox.com/t/telegramlogin-com-faq/53).


### License

TelegramLogin is open-sourced software licensed under [GPL v3.0](http://www.gnu.org/copyleft/gpl.html), documentation under [CC BY 3.0](http://creativecommons.org/licenses/by/3.0/).
