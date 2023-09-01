<?php

namespace api;

function filter(&$data, $filters) {


    foreach ($filters as $key => $filter) {
        $input = filter_input(INPUT_GET, $key);
        $key = $filter['key'] ?? $key;

        foreach ($data as $c => $element) {
            if ($input and!isset($filter['array'])) {
                if (!resolve($element->$key, $input, $filter)) {
                    unset($data[$c]);
                }
            }

            if ($input and isset($filter['array'])) {
                $array = $filter['array'];
                $value = $filter['value'];
                $is_thin = $filter['is_thin'] ?? false;

                $find = false;
                foreach ($element->$array ?? [] as $o => $object) {
                    if (resolve($object->$value, $input, $filter)) {
                        $find = true;
                    } elseif ($is_thin) {
                        unset($data[$c]->$array[$o]);
                    }
                }
                if (!$find) {
                    unset($data[$c]);
                }
            }
        }
    }
}

function resolve($value, $input, $filter) {
    $type = $filter['type'];
    if ($type == 'boolean') {
        if ($value == true and $input == 'true') {
            return true;
        }
        if ($value == false and $input == 'false') {
            return true;
        }
    }
    if ($type == 'substring') {
        if (strpos(strtolower($value), strtolower($input)) !== false) {
            return true;
        }
    }
    if ($type == 'equal') {
        if (strtolower($value) == strtolower($input)) {
            return true;
        }
    }
    if ($type == 'date') {
        $compare = $filter['compare'];
        if ($compare == 'earlier' and strtotime($value) <= strtotime($input)) {
            return true;
        }
        if ($compare == 'later' and strtotime($value) >= strtotime($input)) {
            return true;
        }
    }
    return false;
}
