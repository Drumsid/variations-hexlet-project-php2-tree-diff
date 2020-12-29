<?php

const UNCHANGED = "    ";
const PLUS = "  + ";
const MINUS = "  - ";

function formater($arr, $deep = 0)
{
    $sep = str_repeat('    ', $deep);
    $res = array_map(function ($item) use ($sep, $deep){
        if($item['type'] == 'nested'){
            $tmp = formater($item['value'], $deep + 1);
            return $sep . UNCHANGED . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['type'] == 'unchanged') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . UNCHANGED . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['type'] == 'changed') {
            $tempBefore = arrToStr($item['valueBefore'], $deep + 1);
            $tempAfter = arrToStr($item['valueAfter'], $deep + 1);
            return $sep . MINUS . $item['name'] . ": " . $tempBefore . "\n" . $sep . PLUS . $item['name'] . ": " . $tempAfter . "\n";
        }
        if ($item['type'] == 'removed') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . MINUS . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['type'] == 'added') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . PLUS . $item['name'] . ": " . $tmp . "\n";
        }
        if ($item['type'] == 'return') {
            $tmp = arrToStr($item['value'], $deep + 1);
            return $sep . UNCHANGED . $item['name'] . ": " . $tmp . "\n";
        }
    }, $arr);
        array_unshift($res, "{\n");
        array_push($res, $sep . "}");
    // return implode($res);
    return $res;
}

function arrToStr($arr, $deep)
{
    if (is_array($arr)){
        return formater($arr, $deep);
    } else {
        return $arr;
    }
}

function buldPlain($tree)
{
    $res = array_reduce($tree, function ($acc, $node) {
        if (array_key_exists('type', $node) && $node['type'] == 'nested') {
            $tmp = buldPlain($node['value']);
            $acc = array_merge($acc, $tmp);   
        }
        if (array_key_exists('plain', $node) && $node['type'] == 'changed') {
            $acc[] = "Property '" . substr($node['path'], 1) . "' was updated. From " .
            checkArray($node['valueBefore']) .  " to "  . checkArray($node['valueAfter']) . ".";
        }
        if (array_key_exists('plain', $node) && $node['type'] == 'removed') {
            $acc[] = "Property '" . substr($node['path'], 1) . "' was removed.";
        }
        if (array_key_exists('plain', $node) && $node['type'] == 'added') {
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

function jsonFormat($tree)
{
    $res = array_map(function ($node) {
        if ($node['type'] == 'nested'){
            return [
                    'name' => $node['name'],
                    'type' => $node['type'],
                    'value' => jsonFormat($node['value']),
                ];
        } else {
            if (array_key_exists('valueBefore', $node)) {
                return [
                    'name' => $node['name'],
                    'type' => $node['type'],
                    'valueBefore' => $node['valueBefore'],
                    'valueAfter' => $node['valueAfter'],
                ];
            } else{
                return [
                    'name' => $node['name'],
                    'type' => $node['type'],
                    'value' => $node['value'],
                ];
            }
        }


    }, $tree);
    return $res;
}






























// function xDif($tree)
// {
//     // $res = [];
//     // foreach ($diff as $array) {
//     //     if (array_key_exists('type', $array) && $array['type'] == 'parent') {
//     //         $res['    ' . $array['name']] = xDif($array['value']);
//     //     } else {
//     //         if (array_key_exists('type', $array) && $array['type'] == 'dontChange') {
//     //             $res['    ' . $array['name']] = $array['value'];
//     //         } elseif (array_key_exists('type', $array) && $array['type'] == 'removed') {
//     //             $res['  - ' . $array['name']] = correctStruktures($array['value']);
//     //         } elseif (array_key_exists('type', $array) && $array['type'] == 'added') {
//     //             $res['  + ' . $array['name']] = correctStruktures($array['value']);
//     //         } elseif (array_key_exists('type', $array) && $array['type'] == 'changed') {
//     //             $res['  - ' . $array['name']] = correctStruktures($array['beforeValue']);
//     //             $res['  + ' . $array['name']] = correctStruktures($array['afterValue']);
//     //         }
//     //     }
//     // }

//     $res = array_reduce($tree, function ($acc, $node) {
//         if (array_key_exists('type', $node) && $node['type'] == 'unchanged') {
//             $acc['    ' . $node['name']] = $node['value'];
//         } elseif (array_key_exists('type', $node) && $node['type'] == 'changed') {
//             $acc['  - ' . $node['name']] = $node['valueBefore'];
//             $acc['  + ' . $node['name']] = $node['valueAfter'];
//         } elseif (array_key_exists('type', $node) && $node['type'] == 'removed') {
//             $acc['  - ' . $node['name']] = $node['value'];
//         } elseif (array_key_exists('type', $node) && $node['type'] == 'added') {
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
