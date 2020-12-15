<?php

const UNCHANGED = "    ";
const PLUS = "  + ";
const MINUS = "  - ";

function formaterExplode($arr, $deep = 0)
{
    $sep = str_repeat('    ', $deep);
    $res = array_map(function ($item) use ($sep, $deep){
        if($item['status'] == 'nested'){
            $tmp = formaterExplode($item['value'], $deep + 1);
            return $sep . UNCHANGED . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['status'] == 'unchanged') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . UNCHANGED . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['status'] == 'changed') {
            $tempBefore = arrToStr($item['valueBefore'], $deep + 1);
            $tempAfter = arrToStr($item['valueAfter'], $deep + 1);
            return $sep . MINUS . $item['name'] . ": " . $tempBefore . "\n" . $sep . PLUS . $item['name'] . ": " . $tempAfter . "\n";
        }
        if ($item['status'] == 'removed') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . MINUS . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['status'] == 'added') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . PLUS . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['status'] == 'return') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . UNCHANGED . $item['name'] . ": " . $tmp . "\n";
        }
    }, $arr);
        array_unshift($res, "{\n");
        array_push($res, $sep . "}");
    return implode($res);
}

function arrToStr($arr, $deep)
{
    if (is_array($arr)){
        return formaterExplode($arr, $deep);
    } else {
        return $arr;
    }
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
