<?php

// the identification of this site
define('SITE', '');

// absolute filesystem path to the web root
define('WWW_DIR', dirname(__FILE__));

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

// absolute filesystem path to the temporary files
define('TEMP_DIR', WWW_DIR . '/../temp');


$lifetime = 3600*24*14;
$directoryPath = TEMP_DIR . '/' . 'SessionData';
is_dir($directoryPath) or mkdir($directoryPath, 0777);

if (ini_get("session.use_trans_sid") == true) {
    ini_set("url_rewriter.tags", "");
    ini_set("session.use_trans_sid", false);
}

ini_set("session.gc_maxlifetime", $lifetime);
ini_set("session.gc_divisor", "1");
ini_set("session.gc_probability", "1");
ini_set("session.cookie_lifetime", "0");
ini_set("session.save_path", $directoryPath);



// load bootstrap file
require APP_DIR . '/bootstrap.php';
