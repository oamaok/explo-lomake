<?php

require_once "inc/init.php";

$package = intval($_GET["package"]);

$activities = Activity::model()->findAll("package = ?", $package);

$activities_json = array();

foreach($activities as $activity)
{
    $activity_json = new stdClass;

    $activity_json->id = $activity->id;
    $activity_json->name = $activity->name;
    $activity_json->limit = $activity->participant_limit;
    $activity_json->count = $activity->participant_count;

    $activities_json[] = $activity_json;
}

header("Content-type: text/json");
echo json_encode($activities_json);

?>