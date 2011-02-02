<?php

$current_path = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

require_once( $current_path . 'DataBase.php');
require_once( $current_path . 'DataSource.php');
require_once( $current_path . 'DataImport.php');
require_once( $current_path . 'DataQuery.php');
require_once( $current_path . 'DataColumn.php');
require_once( $current_path . 'DataObject.php');
require_once( $current_path . 'DataCollection.php');

require_once( $current_path . 'spyc.php');

?>