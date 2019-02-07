<?php
/**
 * Plugin Name: Marko Import_PO from CSV
 * Author: Nadiya Markovets
 * Description: Import Purchase Orders data from .csv file.
 * Version: 0.9
 */

if ( ! defined( 'AIFC_BASEDIR' ) ) {
    define( 'AIFC_BASEDIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'marko-import_po' . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'AIFC_TEMPDIR' ) ) {
    define( 'AIFC_TEMPDIR', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'temp-atum-import' .DIRECTORY_SEPARATOR );
}

if ( ! defined( 'AIFC_CLASSESDIR' ) ) {
    define( 'AIFC_CLASSESDIR', AIFC_BASEDIR . 'classes' . DIRECTORY_SEPARATOR );
}

require_once AIFC_BASEDIR . 'functions.php';

if ( ! init() ) {
    return;
}

$app = require_once AIFC_BASEDIR . 'classes' . DIRECTORY_SEPARATOR . 'class-atum-import-from-csv.php';
