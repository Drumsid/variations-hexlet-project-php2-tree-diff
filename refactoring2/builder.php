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
    $res = array_map(function ($key) use ($objBefore, $objAfter, $path) {
        if (
            property_exists($objBefore, $key) && property_exists($objAfter, $key)
            && is_object($objBefore->$key) && is_object($objAfter->$key)
        ) {
            return [
                'name' => $key,
                'type' => 'nested',
                'children' => builder($objBefore->$key, $objAfter->$key, $path . '.' . $key)
            ];
        }
        // else {
        if (
            property_exists($objBefore, $key) && property_exists($objAfter, $key)
            && ($objBefore->$key != $objAfter->$key)
        ) {
            return [
                'name' => $key,
                'type' => 'changed',
                // 'format' => 'plain',
                'path' => $path . '.' . $key,
                'valueBefore' => $objBefore->$key,
                'valueAfter' => $objAfter->$key
            ];
        }
        if (! property_exists($objAfter, $key)) {
            return [
                'name' => $key,
                'type' => 'removed',
                // 'format' => 'plain',
                'path' => $path . '.' . $key,
                'value' => $objBefore->$key
            ];
        }
        if (! property_exists($objBefore, $key)) {
            return [
                'name' => $key,
                'type' => 'added',
                // 'format' => 'plain',
                'path' => $path . '.' . $key,
                'value' => $objAfter->$key
            ];
        } else {
            return [
                'name' => $key,
                'type' => 'unchanged',
                'path' => $path . '.' . $key,
                'value' => $objBefore->$key
            ];
        }
        // }
    }, $unicKey);
    return $res;
}

function stringify($data)
{
    // print_r($data);
    if (is_null($data)) {
        return 'null';
    }
    if (is_bool($data) && $data === true) {
        return 'true';
    }
    if (is_bool($data) && $data === false) {
        return 'false';
    }
    
    if (! is_object($data)) {
        return $data;
    } else {
        $obj = get_object_vars($data);
    }
    $keys = array_keys($obj);
        $res = array_reduce($keys, function ($acc, $key) use ($obj) {
            if (is_object($obj[$key])) {
                $acc[] = [
                    'name' => $key,
                    // 'type' => 'return',
                    'value' => stringify($obj[$key])
                ];
            } else {
                $acc[] = [
                    'name' => $key,
                    // 'type' => 'return',
                    'value' => $obj[$key]
                ];
            }
            return $acc;
        }, []);
        return $res;
}


$objBefore = json_decode($before);
$objAfter = json_decode($after);
$deepObjBefore = json_decode($dBefore);
$deepObjAfter = json_decode($dAfter);

function stylish($arr, $depth = 0)
{
    $sep = str_repeat('    ', $depth);
    $res = array_map(function ($item) use ($sep, $depth) {
        $type = 'none';
        if (array_key_exists('type', $item)) {
            $type = $item['type'];
        }
        switch ($type) {
            case 'nested':
                $children = stylish($item['children'], $depth + 1);
                return $sep . "    " . $item['name'] . " : " . $children . "\n";
            case 'unchanged':
                $unchanged = arrToStr($item['value'], $depth + 1);
                return $sep . "    " . $item['name'] . " : " . $unchanged . "\n";
                // return $sep . "    " . $item['name'] . " : " . $item['value'] . "\n";
            case 'changed':
                $transformedBefore = testStr(stringify($item['valueBefore']), $sep);
                $changedBefore = arrToStr($transformedBefore, $depth + 1);
                $transformedAfter = testStr(stringify($item['valueAfter']), $sep);
                $changedAfter = arrToStr($transformedAfter, $depth + 1);
                return $sep . "  - " . $item['name'] . " : " . $changedBefore . "\n" . $sep .
                "  + " . $item['name'] . " : " . $changedAfter . "\n";
            case 'removed':
                $transformed = testStr(stringify($item['value']), $sep);
                $removed = arrToStr($transformed, $depth + 1);
                return $sep . "  - " . $item['name'] . " : " . $removed . "\n";
            case 'added':
                $transformed = testStr(stringify($item['value']), $sep);
                $added = arrToStr($transformed, $depth + 1);
                return $sep . "  + " . $item['name'] . " : " . $added . "\n";
            default:
                $transformed = testStr(stringify($item['value']), $sep);
                $return = arrToStr($transformed, $depth + 1);
                return $sep . "    " . $item['name'] . " : " . $return . "\n";
        }
    }, $arr);
    // print_r($res);
    // return implode(addBrackets($res, $sep));
    if (is_array($res)) {
        return implode(addBrackets($res, $sep));
    }
    return $res;
    // return addBrackets($res, $sep);
}
function testStr($arr, $sep)
{
    if (!is_array($arr)) {
        return $arr;
    }
    $res = array_map(function ($node) use ($sep) {
        if (is_array($node['value'])) {
            return $sep . "    " . $node['name'] . " : " . testStr($node['value'], $sep) . "\n";
        } else {
            return $sep . "    " . $node['name'] . " : " . $node['value'] . "\n";
        }
    }, $arr);
    // return implode($res);
    return implode(addBrackets($res, $sep));
}
function arrToStr($arr, $depth)
{
    if (is_array($arr)) {
        return stylish($arr, $depth);
    } else {
        return $arr;
    }
}

function addBrackets($tree, $sep)
{
    $first = 0;
    $last = count($tree) - 1;
    $tree[$first] = "{\n" . $tree[$first];
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
// print_r(plain($tree));
// print_r(stylish($tree));
$dTree = builder($deepObjBefore, $deepObjAfter);
// print_r($dTree);
// print_r(plain($dTree));
print_r(stylish($dTree));
