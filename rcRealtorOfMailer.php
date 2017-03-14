<?php
/*
Plugin Name: rcRealtorOfMailer
Plugin URI: http://example.com
Description: realtor of mailer
Version: 1.0.0
Author: alexandrr.naumenko@gmail.com
Author URI: https://freelancehunt.com/freelancer/darkwish.html
License: GPL2
*/

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define('MAILER_DIR', plugin_dir_path(__FILE__));
define('MAILER_URL', plugin_dir_url(__FILE__));
define('INCLUDES_DIR', MAILER_DIR. '..' . DS. '..'. DS. '..' .DS. 'wp-includes');

//ini_set("display_errors",1);
//error_reporting(E_ALL);

spl_autoload_register( 'rcAutoload' );
spl_autoload_register('PHPMailerAutoload');

function rcAutoload($class) 
{
    //if ($class==='PHPMailer') {PHPMailerAutoload($classname); return;}
    $prefix = "mailer\\";
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace("\\", '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

/**
 * PHPMailer SPL autoloader.
 * @param string $classname The name of the class to load
 */
function PHPMailerAutoload($classname)
{
    $prefix = "mailer\\base\\";
    $len = strlen($prefix);
    $relative_class = substr($classname, $len);
    $filename = dirname(__FILE__).DIRECTORY_SEPARATOR .'src'.DS. 'lib' .DS. 
                'PHPMailer' .DS. 'class.'.strtolower($relative_class).'.php';
    if (is_readable($filename)) {
        require $filename;
    }
}

$app = \mailer\Main::instance();
