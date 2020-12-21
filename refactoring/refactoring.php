<?php

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

use Symfony\Component\Yaml\Yaml;



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
  
function myBuilder($objBefore, $objAfter, $path = "")
{
    $unicKey = union(array_keys(get_object_vars($objBefore)), array_keys(get_object_vars($objAfter)));

    $res = array_map(function ($key) use ($objBefore, $objAfter, $path) {
        if (property_exists($objBefore, $key) && property_exists($objAfter, $key) && is_object($objBefore->$key) && is_object($objAfter->$key)) {
            return [
                'name' => $key,
                'status' => 'nested',
                'path' => $path . '.' . $key,
                'value' => myBuilder($objBefore->$key, $objAfter->$key, $path . '.' . $key)
            ];
        }
        if (property_exists($objBefore, $key) && property_exists($objAfter, $key) && ($objBefore->$key == $objAfter->$key)) {
            return [
                'name' => $key,
                'status' => 'unchanged',
                'plain' => 'plain',
                'path' => $path . '.' . $key,
                'value' => boolOrNullToString($objBefore->$key)
            ];
        }
        if (property_exists($objBefore, $key) && property_exists($objAfter, $key) && ($objBefore->$key != $objAfter->$key)) {
            return [
                'name' => $key,
                'status' => 'changed',
                'plain' => 'plain',
                'path' => $path . '.' . $key,
                'valueBefore' => transformObjectToArr(boolOrNullToString($objBefore->$key)),
                'valueAfter' => transformObjectToArr(boolOrNullToString($objAfter->$key))
            ];
        }
        if (property_exists($objBefore, $key) && ! property_exists($objAfter, $key)) {
            return [
                'name' => $key,
                'status' => 'removed',
                'plain' => 'plain',
                'path' => $path . '.' . $key,
                'value' => transformObjectToArr(boolOrNullToString($objBefore->$key))
            ];
        }
        if (! property_exists($objBefore, $key) && property_exists($objAfter, $key)) {
            return [
                'name' => $key,
                'status' => 'added',
                'plain' => 'plain',
                'path' => $path . '.' . $key,
                'value' => transformObjectToArr(boolOrNullToString($objAfter->$key))
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

// simple json
$tree = myBuilder($objBefore, $objAfter);
// print_r($tree);
// print_r(plain($tree));
// print_r(formaterExplode($tree));

//deep json
$deepTree = myBuilder($deepObjBefore, $deepObjAfter);
// print_r($deepTree);
// print_r(plain($deepTree));
// print_r(formaterExplode($deepTree));

<<<<<<< HEAD
// function transformObjectToArr($arr)
// {
//     if (is_object($arr)) {
//         $arr = get_object_vars($arr);
//     } else {
//         return $arr;
//     }
//     $keys = array_keys($arr);
//     $res = array_reduce($keys, function ($acc, $key) use ($arr) {
//         if (is_object($arr[$key])) {
//             $acc[$key] = transformObjectToArr(get_object_vars($arr[$key]));
//         } else {
//             $acc[$key] = $arr[$key];
//         }
//         return $acc;
//     }, []);
//     return $res;
// }
=======
function transformObjectToArr($obj)
{
    if (is_object($obj)) {
        $obj = get_object_vars($obj);
    } else {
        return $obj;
    }
    $keys = array_keys($obj);
    $res = array_reduce($keys, function ($acc, $key) use ($obj) {
        if (is_object($obj[$key])) {
            $acc[] = [
                'name' => $key,
                'status' => 'return',
                'value' => transformObjectToArr($obj[$key])              
            ];
        } else {
            $acc[] = [
                'name' => $key,
                'status' => 'return',
                'value' => $obj[$key]               
            ];
        }
        return $acc;
    }, []);
    return $res;
}
>>>>>>> 32dff5dc6ee6caeb72b1704a479b721a32bbe0a2


// yml test
$ymBefore = Yaml::parse($before,  Yaml::PARSE_OBJECT_FOR_MAP);
// var_dump($ymBefore);
$ymAfter = Yaml::parse($after,  Yaml::PARSE_OBJECT_FOR_MAP);
// var_dump($ymAfter);

$ymBeforeDeep = Yaml::parse($dBefore,  Yaml::PARSE_OBJECT_FOR_MAP);
// var_dump($ymBefore);
$ymAfterDeep = Yaml::parse($dAfter,  Yaml::PARSE_OBJECT_FOR_MAP);
// var_dump($ymAfter);

//test yml simple
$ymTree = myBuilder($ymBefore, $ymAfter);
// print_r($ymTree);
// print_r(plain($ymTree));
// print_r(formaterExplode($ymTree));

//test yml deep
$ymTreeDeep = myBuilder($ymBeforeDeep, $ymAfterDeep);
// print_r($ymTreeDeep);
// print_r(plain($ymTreeDeep));
// print_r(formaterExplode($ymTreeDeep));
