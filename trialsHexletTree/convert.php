<?php

// Реализуйте функцию convert, которая принимает на вход массив определённой структуры и возвращает ассоциативный
//  массив, полученный из этого массива.

// Исходный массив устроен таким образом, что с помощью него можно представлять ассоциативные массивы.
// Каждое значение внутри него — это массив из двух элементов, где первый элемент — ключ, а второй — значение.
// В свою очередь, если значение тоже является массивом, то считается, что это вложенное представление
// ассоциативного массива. Другими словами, любой массив внутри исходного массива всегда рассматривается как данные,
// которые нужно конвертировать в элемент ассоциативного массива.


//convert([]); // []
//convert([['key', 'value']]); // [ 'key' => 'value' ]
//convert([['key', 'value'], ['key2', 'value2']]); // [ 'key' => 'value', 'key2' => 'value2']

// convert([
//   ['key', [['key2', 'anotherValue']]],
//   ['key2', 'value2']
// ]);
// [ 'key' => ['key2' => 'anotherValue'], 'key2' => 'value2' ]


$arr = [['key', 'value'], ['key2', 'value2']];
$arr2 = [
  ['key', [['key2', 'anotherValue']]],
  ['key2', 'value2']
];

// my solution
function convert($arr)
{
    if (count($arr) == 0) {
        return $arr;
    }
    $res = array_reduce(
        $arr,
        function ($acc, $item) {
            if (is_array($item[1])) {
                $item[1] = convert($item[1]);
                $acc[$item[0]] = $item[1];
            } else {
                $acc[$item[0]] = $item[1];
            }
      
            return $acc;
        }
    );
    return $res;
}

print_r(convert($arr2));

// hexlet solution
// BEGIN
function convert2($arr)
{
    $result = array_reduce($arr, function ($acc, $item) {
        [$key, $value] = $item;
        $newValue = is_array($value) ? convert2($value) : $value;
        return array_merge($acc, [$key => $newValue]);
    }, []);

    return $result;
}
// END
