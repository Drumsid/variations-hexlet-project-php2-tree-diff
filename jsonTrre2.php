<?php

// ПОДОГНАЛ ПОД ОТВЕТ!!!!

// сравнение сложных json (данные обрабатываются массивами не объектами)вроде работает но надо править функцию
//  парсинга и писать функцию для красивовго вывода данных
// работает даже с простыми JSON данными которые без вложений, но вывод надо править.

// простые, без вложений JSON  данные (взял из проекта хекслет шаг 3)
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

// тестовые данные массивом
$arrBefore = [
  'a' => 1,
  'c' => [
    'd' => 4,
    'e' => 5,
    'f' => 6
  ],
  'x' => 'yes',
  'z' => 23
];
$arrAfter = [
  'a' => 1,
  'c' => [
    'd' => 4,
    'e' => 6,
    'g' => 7
  ],
  'x' => 'no',
  'h' => 8
];

// json данные с вложенной структурой (взял из проекта хекслет шаг 6)
$before2 = '{
  "a": 1,
  "b": 2,
  "d": 3,
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
$after2 = '{
  "a": 1,
  "b": null,
  "z": "add",
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
// правильный вывод сравнения $before2 и $after2 сделал для тестов. $before2 и $after2 отличаются
// от представленных на хекслете, просто добавил некоторые свои данные
$resultBefore2AndAfter2 = '{" a":1,"-b":2,"+b":"null","common":{"+follow":"false"," setting1":"Value 1","-setting2":200,"-setting3":"true","+setting3":"null","+setting4":"blah blah","+setting5":{"key5":"value5"},"setting6":{"doge":{"-wow":"","+wow":"so much"}," key":"value","+ops":"vops"}},"-d":3,"group1":{"-baz":"bas","+baz":"bars"," foo":"bar","-nest":{"key":"value"},"+nest":"str"},"-group2":{"abc":12345,"deep":{"id":45}},"+group3":{"fee":100500,"deep":{"id":{"number":45}}},"+z":"add"}';
function deepDiff($arrBefore, $arrAfter, $acc = [])
{
    foreach ($arrBefore as $keyBefore => $valBefore) {
        foreach ($arrAfter as $keyAfter => $valAfter) {
            if (array_key_exists($keyBefore, $arrAfter)) {
              // равны ли ключи
                if ($keyBefore == $keyAfter && is_array($valBefore) && is_array($valAfter)) {
                    $acc[$keyBefore] = deepDiff($valBefore, $valAfter);
                } elseif ($keyBefore == $keyAfter && $valBefore == $valAfter) {
                    $acc[$keyBefore] = ['value' => $valBefore, 'status' => 'dontChange'];
                    break;
                } elseif ($keyBefore == $keyAfter && $valBefore != $valAfter) {
                    // $valAfter = is_bool($valAfter) || is_null($valAfter) ? boolOrNullToString($valAfter) : $valAfter;
                    $valAfter = boolOrNullToString($valAfter);
                    // $valBefore = is_bool($valBefore) || is_null($valBefore) ? boolOrNullToString($valBefore) : $valBefore;
                    $valBefore = boolOrNullToString($valBefore);
                    $acc[$keyBefore] = ['beforeValue' => $valBefore, 'afterValue' => $valAfter, 'skip' => true];
                    break;
                }
            } else {
                $acc[$keyBefore] = ['value' => $valBefore, 'status' => 'removed', 'skip' => true];
                break;
            }
        }
    }
    foreach ($arrAfter as $keyAfter => $valAfter) {
        if (! array_key_exists($keyAfter, $arrBefore)) {
            // $valAfter = is_bool($valAfter) || is_null($valAfter) ? boolOrNullToString($valAfter) : $valAfter;
            $valAfter =  boolOrNullToString($valAfter);
            $acc[$keyAfter] = ['value' => $valAfter, 'status' => 'added', 'skip' => true];
        }
    }
    ksort($acc);
    return $acc;
    // return sortArr($acc);
}

function xDif($diff)
{
    $res = [];
    foreach ($diff as $key => $array) {
        if (is_array($array) && is_array(reset($array)) && ! array_key_exists('skip', $array) /*&& ! array_key_exists('beforeValue', $array)*/) {
            $res[$key] = xDif($array);
        } else {
            if (array_key_exists('status', $array) && $array['status'] == 'dontChange') {
                $res[' ' . $key] = $array['value'];
            } elseif (array_key_exists('status', $array) && $array['status'] == 'removed') {
                $res['-' . $key] = $array['value'];
            } elseif (array_key_exists('status', $array) && $array['status'] == 'added') {
                $res['+' . $key] = $array['value'];
            } elseif (array_key_exists('beforeValue', $array) && array_key_exists('afterValue', $array)) {
                $res['-' . $key] = $array['beforeValue'];
                $res['+' . $key] = $array['afterValue'];
            }
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

function correctCurleBrackets($str, $delimiter)
{
    $search = "";
    for ($i = 0; $i < strlen($str); $i++) {
        $search .= $str[$i];
        if ($i == 0) {
            $search .= $delimiter;
        }
        if ($i == strlen($str) - 2) {
            $search .= $delimiter;
        }
    }
    return $search;
}
// преобразуем из json в массив
$before2 = json_decode($before2, true);
$after2 = json_decode($after2, true);

// парсим значения
$diff = deepDiff($before2, $after2);
// print_r($diff);

// выводим сравнение файлов
// print_r(xDif($diff));
  
$strJson = json_encode(xDif($diff));
// тестируем вывод 
var_dump($strJson === $resultBefore2AndAfter2);
$tmp = correctCurleBrackets(str_replace([',', ':{', '}'], [PHP_EOL, ':{' . PHP_EOL, PHP_EOL . '}'], $strJson), PHP_EOL);

// file_put_contents('resultDiff2.txt', $strJson);
// file_put_contents('resultArrayDiff2.txt', $diff);
// красивый вывод данных, пока не работает
// var_dump(str_replace('"', "", $tmp));
