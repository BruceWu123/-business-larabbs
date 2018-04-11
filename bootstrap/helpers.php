<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function make_excerpt($value,$length=200){
    $except =trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));

    return str_limit($except,$length);
}