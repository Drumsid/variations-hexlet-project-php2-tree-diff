<?php

require_once __DIR__ . '/../reduce_version_tree/lib.php';
// require_once __DIR__ . '/newLib.php';
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


function builder($objBefore, $objAfter, $path = "")
{
    $unicKey = union(array_keys(get_object_vars($objBefore)), array_keys(get_object_vars($objAfter)));
    sort($unicKey);
    // print_r(array_values($unicKey));
    $res = array_map(function ($key) use ($objBefore, $objAfter, $path) {
        if (
            property_exists($objBefore, $key) && property_exists($objAfter, $key)
            && is_object($objBefore->$key) && is_object($objAfter->$key)
            // is_object($objBefore->$key) && is_object($objAfter->$key)
        ) {
            return [
                'name' => $key,
                'type' => 'nested',
                // 'path' => $path . '.' . $key,
                // 'value' => builder($objBefore->$key, $objAfter->$key, $path . '.' . $key)
                'children' => builder($objBefore->$key, $objAfter->$key, $path . '.' . $key)
            ];
        } else {
            if (
                property_exists($objBefore, $key) && property_exists($objAfter, $key)
                && ($objBefore->$key != $objAfter->$key)
            ) {
                return [
                    'name' => $key,
                    'type' => 'changed',
                    'format' => 'plain',
                    'path' => $path . '.' . $key,
                    // 'valueBefore' => transformObjectToArr(boolOrNullToString($objBefore->$key)),
                    // 'valueAfter' => transformObjectToArr(boolOrNullToString($objAfter->$key))
                    'valueBefore' => $objBefore->$key,
                    'valueAfter' => $objAfter->$key
                ];
            } elseif (/*property_exists($objBefore, $key) &&*/ ! property_exists($objAfter, $key)) {
                return [
                    'name' => $key,
                    'type' => 'removed',
                    'format' => 'plain',
                    'path' => $path . '.' . $key,
                    // 'value' => transformObjectToArr(boolOrNullToString($objBefore->$key))
                    'value' => $objBefore->$key
                ];
            } elseif (! property_exists($objBefore, $key)/* && property_exists($objAfter, $key)*/) {
                return [
                    'name' => $key,
                    'type' => 'added',
                    'format' => 'plain',
                    'path' => $path . '.' . $key,
                    // 'value' => transformObjectToArr(boolOrNullToString($objAfter->$key))
                    'value' => $objAfter->$key
                ];
            } else {
                return [
                    'name' => $key,
                    'type' => 'unchanged',
                    'format' => 'plain',
                    'path' => $path . '.' . $key,
                    // 'value' => boolOrNullToString($objBefore->$key)
                    'value' => $objBefore->$key
                ];
            }
        }
    }, $unicKey);

    // usort($res, function ($item1, $item2) {
    //     if ($item1['name'] == $item2['name']) {
    //         return 0;
    //     }
    //     return ($item1['name'] < $item2['name']) ? -1 : 1;
    // });
    return $res;
}

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
                'type' => 'return',
                'value' => transformObjectToArr($obj[$key])
            ];
        } else {
            $acc[] = [
                'name' => $key,
                'type' => 'return',
                'value' => $obj[$key]
            ];
        }
        return $acc;
    }, []);
    return $res;
}

function boolOrNullToString($data)
{
    if (is_null($data)) {
        return 'null';
    }
    if (is_bool($data) && $data === true) {
        return 'true';
    }
    if (is_bool($data) && $data === false) {
        return 'false';
    }
    return $data;
}

$objBefore = json_decode($before);
$objAfter = json_decode($after);
$deepObjBefore = json_decode($dBefore);
$deepObjAfter = json_decode($dAfter);

// const UNCHANGED = "    ";
// const PLUS = "  + ";
// const MINUS = "  - ";

function stylish($arr, $deep = 0)
{
    $sep = str_repeat('    ', $deep);
    $res = array_map(function ($item) use ($sep, $deep) {
        // if ($item['type'] == 'nested') {
        //     $tmp = stylish($item['children'], $deep + 1);
        //     return $sep . "    " . $item['name'] . " : " . $tmp . "\n";
        // }
        // if ($item['type'] == 'unchanged') {
        //     $tmp = arrToStr($item['value'], $deep + 1);
        //     return $sep . "    " . $item['name'] . " : " . $tmp . "\n";
        // }
        // if ($item['type'] == 'changed') {
        //     // $tempBefore = arrToStr($item['valueBefore'], $deep + 1);
        //     // $tempAfter = arrToStr($item['valueAfter'], $deep + 1);
        //     $tempBefore = transformObjectToArr(boolOrNullToString($item['valueBefore']));
        //     $tempBefore = arrToStr($tempBefore, $deep + 1);
        //     $tempAfter = transformObjectToArr(boolOrNullToString($item['valueAfter']));
        //     $tempAfter = arrToStr($tempAfter, $deep + 1);
        //     return $sep . "  - " . $item['name'] . " : " . $tempBefore . "\n" . $sep .
        //     "  + " . $item['name'] . " : " . $tempAfter . "\n";
        // }
        // if ($item['type'] == 'removed') {
        //     // $tmp = arrToStr($item['value'], $deep + 1);
        //     $tmp = transformObjectToArr(boolOrNullToString($item['value']));
        //     $tmp = arrToStr($tmp, $deep + 1);
        //     return $sep . "  - " . $item['name'] . " : " . $tmp . "\n";
        // }
        // if ($item['type'] == 'added') {
        //     // $tmp = arrToStr($item['value'], $deep + 1);
        //     $tmp = transformObjectToArr(boolOrNullToString($item['value']));
        //     $tmp = arrToStr($tmp, $deep + 1);
        //     return $sep . "  + " . $item['name'] . " : " . $tmp . "\n";
        // }
        // if ($item['type'] == 'return') {
        //     // $tmp = arrToStr($item['value'], $deep + 1);
        //     $tmp = transformObjectToArr(boolOrNullToString($item['value']));
        //     $tmp = arrToStr($tmp, $deep + 1);
        //     return $sep . "    " . $item['name'] . " : " . $tmp . "\n";
        // }
        switch ($item['type']) {
            case 'nested':
                $tmp = stylish($item['children'], $deep + 1);
                return $sep . "    " . $item['name'] . " : " . $tmp . "\n";
            case 'unchanged':
                $tmp = arrToStr($item['value'], $deep + 1);
                return $sep . "    " . $item['name'] . " : " . $tmp . "\n";
            case 'changed':
                $tempBefore = transformObjectToArr(boolOrNullToString($item['valueBefore']));
                $tempBefore = arrToStr($tempBefore, $deep + 1);
                $tempAfter = transformObjectToArr(boolOrNullToString($item['valueAfter']));
                $tempAfter = arrToStr($tempAfter, $deep + 1);
                return $sep . "  - " . $item['name'] . " : " . $tempBefore . "\n" . $sep .
                "  + " . $item['name'] . " : " . $tempAfter . "\n";
            case 'removed':
                $tmp = transformObjectToArr(boolOrNullToString($item['value']));
                $tmp = arrToStr($tmp, $deep + 1);
                return $sep . "  - " . $item['name'] . " : " . $tmp . "\n";
            case 'added':
                $tmp = transformObjectToArr(boolOrNullToString($item['value']));
                $tmp = arrToStr($tmp, $deep + 1);
                return $sep . "  + " . $item['name'] . " : " . $tmp . "\n";
            case 'return':
                $tmp = transformObjectToArr(boolOrNullToString($item['value']));
                $tmp = arrToStr($tmp, $deep + 1);
                return $sep . "    " . $item['name'] . " : " . $tmp . "\n";
        }
    }, $arr);
    return implode(addBrackets($res, $sep));
}

function arrToStr($arr, $deep)
{
    if (is_array($arr)) {
        return stylish($arr, $deep);
    } else {
        return $arr;
    }
}

function addBrackets($tree, $sep)
{
    $last = count($tree) - 1;
    $tree[0] = "{\n" . $tree[0];
    $tree[$last] = $tree[$last] . $sep . "}";
    return $tree;
}

function buldPlain($tree)
{
    $res = array_reduce($tree, function ($acc, $node) {
        if (array_key_exists('type', $node) && $node['type'] == 'nested') {
            $tmp = buldPlain($node['children']);
            $acc = array_merge($acc, $tmp);
        }
        if (array_key_exists('format', $node) && $node['type'] == 'changed') {
            $node['valueBefore'] = transformObjectToArr(boolOrNullToString($node['valueBefore']));
            $node['valueAfter'] = transformObjectToArr(boolOrNullToString($node['valueAfter']));
            $acc[] = "Property '" . substr($node['path'], 1) . "' was updated. From " .
            checkArray($node['valueBefore']) .  " to "  . checkArray($node['valueAfter']) . ".";
        }
        if (array_key_exists('format', $node) && $node['type'] == 'removed') {
            $acc[] = "Property '" . substr($node['path'], 1) . "' was removed.";
        }
        if (array_key_exists('format', $node) && $node['type'] == 'added') {
            $node['value'] = transformObjectToArr(boolOrNullToString($node['value']));
            $acc[] = "Property '" . substr($node['path'], 1) . "' was added with value: " .
            checkArray($node['value']) . ".";
        }
        return $acc;
    }, []);
    return $res;
}

function checkArray($val)
{
    if (is_array($val)) {
        return "[complex value]";
    }
    return "'" . $val . "'";
}

function plain($arr)
{
    return implode("\n", buldPlain($arr));
}


$tree = builder($objBefore, $objAfter);
// print_r($tree);
print_r(plain($tree));
// print_r(stylish($tree));
$dTree = builder($deepObjBefore, $deepObjAfter);
// print_r($dTree);
// print_r(plain($dTree));
// print_r(stylish($dTree));
