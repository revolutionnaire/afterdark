Around After Dark
==========

A WordPress site for travel and food after the sun sets.

### Setup Local Environment
Install PHP 7.4 and MySQL to your system first.

Run MySQL by typing `brew services start mysql`.

Type `mysql -u root` to run the MySQL prompt.

Type `CREATE DATABASE aroundafterdark;` in the MySQL prompt to create a database for WordPress.

Type `SHOW DATABASES;` to check if your database was successfully created.

Go to the theme folder `wp-content/themes/after-dark` and type `npm install` to install all the dependencies. Then run `npm run sass` to automate rebuilding of the `style.css` of the site's theme.

In the theme folder, type  `npm run build` update the About page template's JS file. You can also run `npm run watch` to automate the rebuilding.

Type `php -S localhost:8000` to start.

Setup WordPress database by going to `localhost:8000` on your browser. Follow the instructions carefully.

#### License
This page is published under [**GNU General Public License**](/LICENSE)
