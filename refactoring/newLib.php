<?php

const UNCHANGED = "    ";
const PLUS = "  + ";
const MINUS = "  - ";

function formater($tree)
{
    $str = "{\n";
    $res = array_reduce($tree, function ($acc, $item) {
        if ($item['status'] == 'changed') {
            $acc .= MINUS . $item['name'] . ": " . $item['valueBefore'] . "\n";
            $acc .= PLUS . $item['name'] . ": " . $item['valueAfter'] . "\n";
        } elseif ($item['status'] == 'unchanged') {
            $acc .= UNCHANGED . $item['name'] . ": " . $item['value'] . "\n";
        } elseif ($item['status'] == 'removed') {
            $acc .= MINUS . $item['name'] . ": " . $item['value'] . "\n";
        } elseif ($item['status'] == 'added') {
            $acc .= PLUS . $item['name'] . ": " . $item['value'] . "\n";
        }
        return $acc;
    }, $str);
    return $res . "}";
}

// function xDif($tree)
// {
//     // $res = [];
//     // foreach ($diff as $array) {
//     //     if (array_key_exists('type', $array) && $array['type'] == 'parent') {
//     //         $res['    ' . $array['name']] = xDif($array['value']);
//     //     } else {
//     //         if (array_key_exists('status', $array) && $array['status'] == 'dontChange') {
//     //             $res['    ' . $array['name']] = $array['value'];
//     //         } elseif (array_key_exists('status', $array) && $array['status'] == 'removed') {
//     //             $res['  - ' . $array['name']] = correctStruktures($array['value']);
//     //         } elseif (array_key_exists('status', $array) && $array['status'] == 'added') {
//     //             $res['  + ' . $array['name']] = correctStruktures($array['value']);
//     //         } elseif (array_key_exists('status', $array) && $array['status'] == 'changed') {
//     //             $res['  - ' . $array['name']] = correctStruktures($array['beforeValue']);
//     //             $res['  + ' . $array['name']] = correctStruktures($array['afterValue']);
//     //         }
//     //     }
//     // }

//     $res = array_reduce($tree, function ($acc, $node) {
//         if (array_key_exists('status', $node) && $node['status'] == 'unchanged') {
//             $acc['    ' . $node['name']] = $node['value'];
//         } elseif (array_key_exists('status', $node) && $node['status'] == 'changed') {
//             $acc['  - ' . $node['name']] = $node['valueBefore'];
//             $acc['  + ' . $node['name']] = $node['valueAfter'];
//         } elseif (array_key_exists('status', $node) && $node['status'] == 'removed') {
//             $acc['  - ' . $node['name']] = $node['value'];
//         } elseif (array_key_exists('status', $node) && $node['status'] == 'added') {
//             $acc['  + ' . $node['name']] = $node['value'];
//         }
//         return $acc;
//     }, []);
//     return $res;
// }

// function stylish($arr, $deep = 0)
// {
//     $sep = str_repeat('    ', $deep);
//     $res = "{\n";
//     foreach ($arr as $key => $val) {
//         if (is_array($val)) {
//             $tmp = stylish($val, $deep + 1);
//             $res .= $sep . $key . " : " . $tmp;
//         } else {
//             $res .= $sep . $key . " : " . $val . "\n";
//         }
//     }
//     // array_reduce($arr, function ($acc, $item) {
//     //     $acc .= $sep .
//     // }, $res);
//     return $res . $sep . "}\n";
// }
