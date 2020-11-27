<?php

function transformToArrAndPath($tree, $path = "") // добавил путь к файлу от корня
{
    $res = [];
  
    foreach ($tree as $key => $val) {
        if (is_object($val)) {
            $res[] = ['name' => $key,  'type' => 'parent', 'path' => $path . '.' . $key, 'value' => transformToArrAndPath($val, $path . '.' . $key)];
        } else {
            $res[] = ['name' => $key, 'path' => $path . '.' . $key, 'value' => boolOrNullToString($val)];
        }
    }
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

function findSameName($findArr, $dataArrs)
{
    ['name' => $findName] = $findArr;
    foreach ($dataArrs as $dataArr) {
        if ($findName == $dataArr['name']) {
            return $dataArr;
        }
    }
    return false;
}

function differ($beforeTree, $afterTree, $res = [])
{
    // $res = [];
    foreach ($beforeTree as $beforeValue) {
        $findName = findSameName($beforeValue, $afterTree);
        if ($findName) {
            if (($beforeValue['name'] == $findName['name']) && array_key_exists('type', $findName) && $findName['type'] == 'parent') {
                $beforeValue['value'] = differ($beforeValue['value'], $findName['value']);
                $res[] = $beforeValue;
            } elseif (($beforeValue['name'] == $findName['name']) && ($beforeValue['value'] == $findName['value'])) {
                $beforeValue['status'] = 'dontChange';
                $beforeValue['plain'] = 'plain';
                $res[] = $beforeValue;
            } elseif (($beforeValue['name'] == $findName['name']) && ($beforeValue['value'] != $findName['value'])) {
                $beforeValue['status'] = 'changed';
                $beforeValue['plain'] = 'plain';
                $beforeValue['beforeValue'] = $beforeValue['value'];
                $beforeValue['afterValue'] = $findName['value'];
                if (array_key_exists('type', $beforeValue)) {
                    $beforeValue['type'] = 'skip';
                }
                // unset($beforeValue['value']);
                $res[] = $beforeValue;
            }
        } else {
            $beforeValue['status'] = 'removed';
            $beforeValue['plain'] = 'plain';
            if (array_key_exists('type', $beforeValue)) {
                $beforeValue['type'] = 'skip';
            }
            $res[] = $beforeValue;
        }
    }
    foreach ($afterTree as $aftervalue) {
        $findName = findSameName($aftervalue, $beforeTree);
        if (! $findName) {
            $aftervalue['status'] = 'added';
            $aftervalue['plain'] = 'plain';
            if (array_key_exists('type', $aftervalue)) {
                $aftervalue['type'] = 'skip';
            }
            $res[] = $aftervalue;
        }
    }
    usort($res, function ($item1, $item2) {
        if ($item1['name'] == $item2['name']) {
            return 0;
        }
        return ($item1['name'] < $item2['name']) ? -1 : 1;
    });
    return $res;
}

function correctStructure($arr)
{
    if (! is_array($arr) || (array_key_exists('type', $arr) && $v['type'] == 'skip')) {
        return $arr;
    }
    $res = [];
    foreach ($arr as $v) {
        if (is_array($v) && array_key_exists('type', $v) && $v['type'] == 'parent') {
            $res["    " . $v['name']] = correctStructure($v['value']);
        } else {
            $res["    " . $v['name']] = $v['value'];
        }
    }
    
    return $res;
}

function xDif($diff)
{
    $res = [];
    foreach ($diff as $array) {
        if (array_key_exists('type', $array) && $array['type'] == 'parent') {
            $res['    ' . $array['name']] = xDif($array['value']);
        } else {
            if (array_key_exists('status', $array) && $array['status'] == 'dontChange') {
                $res['    ' . $array['name']] = $array['value'];
            } elseif (array_key_exists('status', $array) && $array['status'] == 'removed') {
                $res['  - ' . $array['name']] = correctStructure($array['value']);
            } elseif (array_key_exists('status', $array) && $array['status'] == 'added') {
                $res['  + ' . $array['name']] = correctStructure($array['value']);
            } elseif (array_key_exists('status', $array) && $array['status'] == 'changed') {
                $res['  - ' . $array['name']] = correctStructure($array['beforeValue']);
                $res['  + ' . $array['name']] = correctStructure($array['afterValue']);
            }
        }
    }
    return $res;
}

function collectPathArr($arr)
{
    $res = [];
    foreach ($arr as $key => $value) {
        if (array_key_exists('type', $value) && $value['type'] == 'parent') {
            $tmp = collectPathArr($value['value']);
            $res = array_merge($res, $tmp); 
        } else {
            if (array_key_exists('plain', $value) && $value['status'] == 'changed') {
                $res[] = "Property '" . substr($value['path'], 1) . "' was updated. From " . checkArray($value['beforeValue']) . " to "  . checkArray($value['afterValue']) . ".";
            }
            if (array_key_exists('plain', $value) && $value['status'] == 'removed') {
                $res[] = "Property '" . substr($value['path'], 1) . "' was removed.";
            }
            if (array_key_exists('plain', $value) && $value['status'] == 'added') {
                $res[] = "Property '" . substr($value['path'], 1) . "' was added with value: " . checkArray($value['value']) . ".";
            }
        }

    }
    return $res;
}

function checkArray ($val)
{
    if (is_array($val)) {
        return "[complex value]";
    }
    return "'" . $val . "'";
}

function plain($arr)
{
    return implode("\n", collectPathArr($arr));
}
$beforeTree = '{
    "host": "hexlet.io",
    "timeout": 50,
    "proxy": "123.234.53.22",
    "follow": false
  }';
$afterTree = '{
    "timeout": 20,
    "verbose": true,
    "host": "hexlet.io"
  }';

$beforeArr = transformToArrAndPath(json_decode($beforeTree));
$afterArr = transformToArrAndPath(json_decode($afterTree));
// print_r($beforeArr);
// print_r($afterArr);

$diffArr = differ($beforeArr, $afterArr);
// print_r($diffArr);

$plained = plain($diffArr);
print_r($plained);

// ==============================================

$testBeforeDeep = '{
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

$testAfterDeep = '{
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

$deepBeforeArr = transformToArrAndPath(json_decode($testBeforeDeep));
$deepAfterArr = transformToArrAndPath(json_decode($testAfterDeep));
// print_r($deepBeforeArr);
// print_r($deepAfterArr);

$deepDiffArr = differ($deepBeforeArr, $deepAfterArr);
// print_r($deepDiffArr);

$deeepPlained = plain($deepDiffArr);
// print_r($deeepPlained);