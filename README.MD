#### To stand up a dev environment:
1. Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads), [Vagrant](https://www.vagrantup.com/downloads.html), [Node.js](https://nodejs.org/en/download/) in order to use node package manager, and [Composer](https://getcomposer.org/download/).
2. ```git clone https://github.com/realjv3/bjjtrackr.git``` to a new project root directory (e.g. ~/code/bjjtrackr).
3. Run ```composer install``` from project root directory.
4. Run ```php vendor/bin/homestead make``` (Linux/Mac) or ```vendor\\bin\\homestead make``` (Windows).
5. Generate keys ```ssh-keygen -t rsa -b 4096 -C "your_email@example.com"```.
6. Change _authorize_ and _keys_ in Homestead.yaml to path where the keys were saved in previous step.
7. Run ```npm install``` from project root directory.
8. Run ```vagrant up``` from the project root directory.
9. Run ```npm run dev``` to develop, ```npm run build``` to deploy.
10. To create the database tables, ```vagrant ssh``` into the virtual machine, ```cd``` into the project root and run ```php artisan migrate```.
11. Then seed the database tables with users and roles with ```php artisan db:seed```.
12. Set the default timezone in php.ini

At this point, the web app should be up at https://flowrolled.local, according to _hostname_ in Homestead.yaml.

#### To deploy on Ubuntu 20.04:
1. sudo apt install nginx
2. sudo systemctl enable nginx
3. sudo systemctl start nginx
4. sudo systemctl status nginx
   for nginx auto-restart
    1. sudo mkdir -p /etc/systemd/system/nginx.service.d/
    1. add lines & save:

       [Service]

       Restart=always

       RestartSec=5s
    1. sudo systemctl daemon-reload
    1. test with`sudo pkill nginx`& after 5s`systemctl status nginx`
5. sudo apt install mariadb-server mariadb-client
6. systemctl status mariadb
7. sudo systemctl enable mariadb
8. sudo mysql_secure_installation
9. sudo mariadb -u root
10. create database flowrolled;
11. exit;
12. sudo apt install php7.4 php7.4-fpm php7.4-mysql php-common php7.4-cli php7.4-common php7.4-json php7.4-opcache php7.4-readline php7.4-mbstring php7.4-xml php7.4-gd php7.4-curl php7.4-zip php7.4-imagick
13. sudo systemctl start php7.4-fpm
14. sudo systemctl enable php7.4-fpm
15. systemctl status php7.4-fpm
16. sudo nano /etc/nginx/sites-enabled/default
17. sudo apt install vsftpd
18. sudo nano /etc/vsftpd.conf (optional)
19. sudo chown -R ubuntu:www-data /var/www/flowrolled && sudo chmod -R 664 /var/www/flowrolled && sudo find /var/www/flowrolled -type d -exec chmod 775 {} +
20. cd ~
21. curl -sS https://getcomposer.org/installer -o composer-setup.php
22. sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
23. rm composer-setup.php
24. cd /var/www/flowrolled
25. composer i
26. sudo apt install nodejs
27. sudo apt install npm
28. sudo npm install -g npm
29. npm i
39. sudo apt install certbot python3-certbot-nginx
31. sudo certbot --nginx -d flowrolled.com
32. php artisan migrate:install
33. php artisan migrate
34. php artisan db:seed
35. sudo npm install -g webpack
36. npm install webpack-cli -g
37. php artisan storage:link
38. set stripe keys in .env file
39. php artisan key:generate
40. php artisan config:clear
41. npm run prod
42. php artisan view:cache
