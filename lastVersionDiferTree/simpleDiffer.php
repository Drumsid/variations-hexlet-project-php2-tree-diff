<?php

// начал писать третий фариант дифа. осталось допидлить красивый вывод. не пойму как написать нормальную функцию. 
// Сейчас все уперлось в то что doge не выводиться в нжном месте, точнее нет отступа

$deepTreeBefore = '{
  "host": "hexlet.io",
  "timeout": 50,
  "json": {
      "test": "value",
      "anower": {
          "pupu": "fer"
      },
      "io": "ss"
  },
  "proxy": "123.234.53.22",
  "follow": false
}';
$deepTreeAfter = '{
    "host": "hexlet.io",
    "timeout": 50,
    "json": {
        "test": "value2",
        "anower": {
            "pupu": "fer",
            "re": "er"
        },
        "samf": "val"
    },
    "proxy": "123.234.53.22",
    "follow": false
  }';

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






// print_r($objTree);

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

function transformToArrAndPath($tree, $path = "") // добавил путь к файлу от корня
{
    $res = [];
  
    foreach ($tree as $key => $val) {
        if (is_object($val)) {
            $res[] = ['name' => $key,  'type' => 'parent', 'path' => $path . '/' . $key, 'value' => transformToArrAndPath($val, $path . '/' . $key)];
        } else {
            $res[] = ['name' => $key, 'path' => $path . '/' . $key, 'value' => boolOrNullToString($val)];
        }
    }
    return $res;
}


function transformToArr($tree)
{
    $res = [];

    foreach ($tree as $key => $val) {
        if (is_object($val)) {
            $res[] = ['name' => $key,  'type' => 'parent', 'value' => transformToArr($val)];
        } else {
            $res[] = ['name' => $key, 'value' => boolOrNullToString($val)];
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
                $res[] = $beforeValue;
            } elseif (($beforeValue['name'] == $findName['name']) && ($beforeValue['value'] != $findName['value'])) {
                $beforeValue['status'] = 'changed';
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
// ========================= simple deff ================================

// $objBeforeTree = json_decode($beforeTree);
// $arrBeforeTree = transformToArr($objBeforeTree);

// $objAfterTree  = json_decode($afterTree);
// $arrAfterTree = transformToArr($objAfterTree);
// print_r(transformToArr($objBeforeTree));
// print_r(transformToArr($objAfterTree));
// $simpleDiff = differ($arrBeforeTree, $arrAfterTree);

// ========================= deep deff ================================

// $objDeepTreeBefore = json_decode($deepTreeBefore);
// $arrDeepTreeBefore = transformToArr($objDeepTreeBefore);

// $objDeepTreeAfter = json_decode($deepTreeAfter);
// $arrDeepTreeAfter = transformToArr($objDeepTreeAfter);
// $deepDeff = differ($arrDeepTreeBefore, $arrDeepTreeAfter);

// ========================= test deep deff ================================

$objtestBeforeDeep = json_decode($testBeforeDeep); 
$arrtestBeforeDeep = transformToArr($objtestBeforeDeep);
// var_dump($objtestBeforeDeep);
// var_dump($arrtestBeforeDeep);

$objtestAfterDeep = json_decode($testAfterDeep);
$arrtestAfterDeep = transformToArr($objtestAfterDeep);
$testdeepDeff = differ($arrtestBeforeDeep, $arrtestAfterDeep);
// print_r($testdeepDeff);

function test($arr){
  if (! is_array($arr) || (array_key_exists('type', $arr) && $v['type'] == 'skip')) {
    return $arr;
  }  
  $res = [];
  foreach ($arr as $v) {
        if (is_array($v) && array_key_exists('type', $v) && $v['type'] == 'parent'){
        $res["    " . $v['name']] = test($v['value']); 
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
                $res['  - ' . $array['name']] = test($array['value']);
            } elseif (array_key_exists('status', $array) && $array['status'] == 'added') {
                $res['  + ' . $array['name']] = test($array['value']);
            } elseif (array_key_exists('status', $array) && $array['status'] == 'changed') {
                $res['  - ' . $array['name']] = test($array['beforeValue']);
                $res['  + ' . $array['name']] = test($array['afterValue']);
            }
        }
    }
    return $res;
}

// print_r(xDif($simpleDiff));
// print_r(xDif($testdeepDeff));


function niceView($arr, $deep = 0) 
{
    $sep = str_repeat('    ', $deep);
    $res = "{\n";
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $tmp = niceView($val, $deep + 1);
            $res .= $sep . $key . " : " . $tmp;
        } else {
            $res .= $sep . $key . " : " . $val . "\n";
        }
    }
    return $res . $sep . "}\n";
}

// print_r(out(json_encode(xDif($simpleDiff))));
// print_r(out(json_encode(xDif($deepDeff))));

print_r(niceView(xDif($testdeepDeff)));


// function out($arr)
// {
//     $res = '';
//     for ($i=0; $i < strlen($arr); $i++) { 
//         if ($arr[$i] == "{" && $arr[$i + 1] == "\"") {
//             $res .= $arr[$i] . "\n";
//             $i++;
//         } else if($arr[$i] == "\"" && $arr[$i + 1] == "}"){
//             $res .= "\n}";
//             $i++;
//         }
//         else if($arr[$i] == "\""){
//             $res .= "";
//         }
//         else if($arr[$i] == "," && $arr[$i + 1] == "\"") {
//             $res .= "\n";
//             $i++;
//         }else {
//             $res .= $arr[$i];
//         }
//     }
//     return $res;
// }