<?php

function deepDiff($beforeTree, $afterTree, $res = [])
{
    foreach ($beforeTree as $beforeValue) {
        $findName = findSameName($beforeValue, $afterTree);
        if ($findName) {
            if (
                ($beforeValue['name'] == $findName['name']) && array_key_exists('type', $findName) &&
                $findName['type'] == 'parent'
            ) {
                $beforeValue['value'] = deepDiff($beforeValue['value'], $findName['value']);
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