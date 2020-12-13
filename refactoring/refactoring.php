<?php

// не работает сравнение вложенных json

require_once __DIR__ . '/../reduce_version_tree/lib.php';
require_once __DIR__ . '/newLib.php';
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

$dBefore = '{
    "common": {
      "setting1": "Value 1",
      "setting2": 200,
      "setting3": true,
      "setting6": {
        "key": "value",
        "doge": {
          "wow": ""
        }
      }
    },
    "group1": {
      "baz": "bas",
      "foo": "bar",
      "nest": {
        "key": "value"
      }
    },
    "group2": {
      "abc": 12345,
      "deep": {
        "id": 45
      }
    }
}';
$dAfter = '{
    "common": {
        "follow": false,
        "setting1": "Value 1",
        "setting3": null,
        "setting4": "blah blah",
        "setting5": {
        "key5": "value5"
        },
        "setting6": {
        "key": "value",
        "ops": "vops",
        "doge": {
            "wow": "so much"
        }
        }
    },
    "group1": {
        "foo": "bar",
        "baz": "bars",
        "nest": "str"
    },
    "group3": {
        "fee": 100500,
        "deep": {
        "id": {
            "number": 45
        }
        }
    }
}';
$objBefore = json_decode($before);
$objAfter = json_decode($after);
$deepObjBefore = json_decode($dBefore);
$deepObjAfter = json_decode($dAfter);
  
function myBuilder($objBefore, $objAfter)
{
    print_r(111);
    $unicKey = array_keys(union(get_object_vars($objBefore), get_object_vars($objAfter)));
    print_r(222);
    $res = array_map(function ($key) use ($objBefore, $objAfter) {
        if (property_exists($objBefore, $key) && property_exists($objAfter, $key) && is_object($objBefore->$key) && is_object($objAfter->$key)) {
            // return [
            //     'name' => $key,
            //     'status' => 'nested',
            //     'value' => myBuilder($objBefore->$key, $objAfter->$key)
            // ];
            // print_r($objBefore->$key);
        }
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

    usort($res, function ($item1, $item2) {
        if ($item1['name'] == $item2['name']) {
            return 0;
        }
        return ($item1['name'] < $item2['name']) ? -1 : 1;
    });
    return $res;
}

$tree = myBuilder($objBefore, $objAfter);
print_r($tree);
// $json = json_encode(xDif($tree));

// print_r(str_replace(["{\"", '","', ',"'], ["{\n", "\n", "\n"], $json));

$test = get_object_vars(json_decode($dBefore));

// print_r(get_object_vars($test['group1']));
