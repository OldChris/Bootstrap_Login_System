<?php

$root="/";
$session="session/";
$bootstrap="bootstrap/";
$scripts="scripts/";
$deeper="session/deeper/";
function makeAbsSitePath($path)
{
    $path2=$GLOBALS[$path];
    if ($path2 == "/")
    {
        $result="/";  // add value of path to prefix
    } 
    else
    {
        $result="/" . $path2;  // add value of path to prefix
    }
    return $result;
}
function makeAbsRootPath($path)
{
    $prefix=$_SERVER['DOCUMENT_ROOT'];
    $path=$GLOBALS[$path];
    if ($path == "/")
    {
        $result=$prefix . $path;  // add value of path to prefix
    } 
    else
    {
        $result=$prefix . "/" . $path;  // add value of path to prefix
    }
    return $result;
}

function makeRelPath($path, $dir)
{
    $sub=str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir);  // get rid of first part of path
    if ($sub == "/" . $path)
    {
        return "";
    }
    $occ=substr_count($sub, '/');   // count of levels down in subfolders
    $prefix="";
    for ($x = 1; $x <= $occ; $x++)  // construct new prefix
    {
        $prefix.= "../";      
    } 
    $result=$prefix . $GLOBALS[$path];  // add value of path to prefix
    return $result;
}