<?php

require_once __DIR__ . '/../reduce_version_tree/lib.php';
$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use function Funct\Collection\union;

$before = '{
    "host": "hexlet.io",
    "timeout": 50,
    "proxy": "123.234.53.22",
    "follow": false
  }';
$after = '{
"timeout": 20,
"verbose": true,
"host": "hexlet.io"
}';
$objBefore = json_decode($before);
$objAfter = json_decode($after);
  
function myBuilder($objBefore, $objAfter)
{
    $unicKey = array_keys(union(get_object_vars($objBefore), get_object_vars($objAfter)));
    $res = array_map(function ($key) use ($objBefore, $objAfter) {
        if (property_exists($objBefore, $key) && property_exists($objAfter, $key) && ($objBefore->$key == $objAfter->$key)) {
            return [
                'name' => $key,
                'status' => 'unchanged',
                'value' => boolOrNullToString($objBefore->$key)
            ];
        }
        if (property_exists($objBefore, $key) && property_exists($objAfter, $key) && ($objBefore->$key != $objAfter->$key)) {
            return [
                'name' => $key,
                'status' => 'changed',
                'valueBefore' => boolOrNullToString($objBefore->$key),
                'valueAfter' => boolOrNullToString($objAfter->$key)
            ];
        }
        if (property_exists($objBefore, $key) && ! property_exists($objAfter, $key)) {
            return [
                'name' => $key,
                'status' => 'removed',
                'value' => boolOrNullToString($objBefore->$key)
            ];
        }
        if (! property_exists($objBefore, $key) && property_exists($objAfter, $key)) {
            return [
                'name' => $key,
                'status' => 'added',
                'value' => boolOrNullToString($objAfter->$key)
            ];
        }
    }, $unicKey);
    return $res;
}
// print_r(get_object_vars($objBefore));
// print_r(get_object_vars($objAfter));
// print_r(array_keys(union(get_object_vars($objBefore), get_object_vars($objAfter))));
// $test = 'host';
// if (property_exists($objBefore, $test)) {
//     print_r($objBefore->$test);
// }

print_r(myBuilder($objBefore, $objAfter));
