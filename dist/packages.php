<?php

require_once "inc/init.php";

$packages = Package::model()->findAll();

$packages_json = array();

foreach($packages as $package)
{
    $package_json = new stdClass;

    $package_json->id = $package->id;
    $package_json->name = $package->name;

    $packages_json[] = $package_json;
}

header("Content-type: text/json");
echo json_encode($packages_json);

?>