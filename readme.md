
My enviroment was Windows 10 64bit and for this project I've used:

Laragon Full 3.1.6 https://laragon.org/download/
  include: php 7.1.7 Win32 VC14 x64, apache httpd 2.4.27 win64 VC14, mysql 5.7.19 winx64, node.js 6.11.3-x64
  
HeidiSQL https://www.heidisql.com/download.php
  some credentional I've used for database... user: root, password: , Hostname IP: 127.0.0.1
  
To install:
  - Copy folder blackjack to x:/laragon/www (default laragon path).
  - Make database called "blackjack" in MySql throw Largon menu.
    migrations will struct your tables
  - Use Laragon termina and execute: 
      "php artisan app:name blackjack" as said  https://laravel.com/docs/5.0/configuration#introduction
      "php artisan migrate" as said https://laravel.com/docs/5.6/migrations
      "php db:seed" as said https://laravel.com/docs/5.6/seeding
      
To run:
  - Disable firewall.
  - in PHP extentions make sure "mbstring" and "sockets" are activated throe Laragon menu.
  - Open two Laragon terminal (with admin privileges) and execute:
    on /var/www/html/blackjack:  forever start /public/js/vueapp.js
      alternative use: npm run watch-poll
    on var/www/html/blackjack/dediserver: node gameserver.js
    
To enter:
  - Main link website: http://blackjack.dev/#/login
  
Author: Leandro Silva <@gmail.com>

Please be kind and support my work for further release, any donation is apreciated
at https://www.paypal.me/LeandroAdonis/5 for 5€ or 5$ amount. Any amount you wish, 
I will be very gratefull and real happy to see my work worth something to you. Thank you.
