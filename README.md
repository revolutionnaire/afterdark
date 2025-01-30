After Dark
==========

A WordPress site for travel and food after the sun sets.

Setup Local Environment
-----------------------

1. Install PHP 7.4, Composer, and MySQL to your system first.

2. Run the background service of MySQL.

```
brew services start mysql
```

3. Run the MySQL prompt.

```
mysql -u root
```

4. Create the database for WordPress.

```
CREATE DATABASE afterdark;
```

5. Check if your database was successfully created.

```
SHOW DATABASES;
```

6. Go to the theme folder `wp-content/themes/neonsunset` and install the theme dependencies. You can view the list of dependencies in `package.json` located in the theme's folder.

```
npm install
```

7. Run Sass to automate compiling of the theme's style.

```
npm run sass
```

8. Run Parcel to automate rebuilding of the theme's JavaScript files.

```
npm run watch
```

9. Run the PHP server.

```
php -S localhost:8000
```

10. Install PHP_CodeSniffer using Composer.

```
composer global require "squizlabs/php_codesniffer=*"
```

11. Install the PHP_CodeSniffer Composer Installer along with the WordPress Coding Standard.

```
composer global require wp-coding-standards/wpcs dealerdirect/phpcodesniffer-composer-installer
```

12. Check if the WordPress Coding Standard was added to PHP_CodeSniffer. ``.

```
phpcs -i
```

13. Setup WordPress database by going to `localhost:8000` on your browser. Follow the instructions carefully.

14. You can check your codes if they follow WordPress Coding Conventions.

```
phpcs --standard=WordPress path/to/file
```

15. Don't forget to compress the JavaScript files before you push any changes to the remote repository.

```
npm run build
```

#### License
This page is published under [**GNU General Public License**](/LICENSE)
