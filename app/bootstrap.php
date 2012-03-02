<?php

/**
 * My Application bootstrap file.
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */

setLocale(LC_ALL, "cs_CZ.utf-8");



// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR . '/Nette/loader.php';



// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Debug::enable();

// 2b) load configuration from config.ini file
Environment::loadConfig();


$session = Environment::getSession();
$session->setExpiration("+ 14 days");
$session->start();

$user = Environment::getUser();
$user->setNamespace('halas');
$user->setExpiration("+ 7 days", FALSE);

// Step 3: Configure application
// 3a) get and setup a front controller
$application = Environment::getApplication();
$application->errorPresenter = 'Front:Error';
//$application->catchExceptions = TRUE;
Debug::$strictMode = TRUE;



/*$user = Environment::getUser();
$user->setExpiration("+ 365 days", FALSE);*/


HTML::$xhtml = FALSE;

// Step 4: Setup application router
$router = $application->getRouter();

Route::setStyleProperty('action', Route::FILTER_TABLE, array(
	'projektove-kolo' => 'round',
	'projekt' => 'project'
));

Route::setStyleProperty('presenter', Route::FILTER_TABLE, array(
    'hodnoceni' => 'Homepage',
	'prihlaseni' => 'Login',
));

$router[] = new Route('index.php', array(
	'module' => 'Front',
	'presenter' => 'Homepage',
	'action' => 'default',
), Route::ONE_WAY);

$router[] = new Route('<presenter>/<action>/<id>', array(
    	'module' => 'Front',
	'presenter' => 'Homepage',
	'action' => 'default',
	'id' => NULL,
));


//db
dibi::connect(Environment::getConfig('database'));

// Step 5: Run the application!
$application->run();
