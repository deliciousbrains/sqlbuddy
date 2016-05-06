<?php

function basePath()
{
    return dirname(dirname(__FILE__));
}

function resourcesPath()
{
    return basePath() . '/resources';
}

function storagePath()
{
    return basePath() . '/storage';
}
