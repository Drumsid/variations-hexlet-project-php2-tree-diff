<?php

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

$beforeTree = json_decode($beforeTree);
$afterTree = json_decode($afterTree);

// print_r($objTree);

function test($tree)
{
    $res = [];

    foreach ($tree as $key => $val) {
        if (is_object($val)) {
            $res[] = ['name' => $key, /*'value' => $val,*/ 'meta' => [], 'type' => 'dir', 'children' => test($val)];
        } else {
            $res[] = ['name' => $key, 'meta' => ['value' => boolOrNullToString($val)], 'type' => 'file'];
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
$beforeTree = test($beforeTree);
$afterTree = test($afterTree);

// print_r($beforeTree);
// print_r($afterTree);

// пробую писать дифер функцию
// function differ($beforeTree, $afterTree)
// {
//   $res = array_map(
//     function ($beforeItem) use($afterTree) {
//       return array_map(
//       function ($afterItem) use($beforeItem){
//         if ($afterItem['name'] == $beforeItem['name'] &&
//           $afterItem['meta']['value'] == $beforeItem['meta']['value']
//         ) {
//           return $afterItem;
//         }
//       },
//       $afterTree
//       );
//     },
//     $beforeTree
//   );
//   return $res;
// }

// print_r(differ($beforeTree, $afterTree));
