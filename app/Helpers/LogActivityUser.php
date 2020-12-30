<?php


 function log_activity($subject,$url,$method,$idusers)
{
    $log = new App\Models\LogActivities;
    $log->subject = $subject;
    $log->url = $url;
    $log->method = $method;
    $log->idusers = $idusers;
    $log->save();

    return $log;

}