# Slim Framework 3 Skeleton Application

Use this skeleton application to quickly setup and start working on a new Slim Framework 3 application. This application uses the latest Slim 3 with the PHP-View template renderer, Monolog logger, Laravel Mix as a webpack wrapper. 

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application.

    composer create-project HDFChomeloan/slim-skeleton [my-app-name]

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* You can change `index.php` file for settings if you want to Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writeable.

To run the application in development, you can run these commands 

	cd [my-app-name]
	composer install
	npm install

Run this command in the application directory to run the test suite

	php composer.phar test

That's it! Now go build something cool.
"# digi-docs" 
