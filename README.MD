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

At this point, the web app should be up at https://bjjtrackr.local, according to _hostname_ in Homestead.yaml.

#### To deploy:
Coming soon...

