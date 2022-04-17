<?php

namespace api;

function filter(&$data, $filters) {


    foreach ($filters as $key => $filter) {
        $input = filter_input(INPUT_GET, $key);
        $key = $filter['key'] ?? $key;
        $type = $filter['type'];
        if ($input and $type == 'exists') {
            foreach ($data as $c => $element) {
                $array = $filter['array'];
                $value = $filter['value'];
                $find = false;
                foreach ($element->$array ?? [] as $object) {
                    if (strtolower($input) == strtolower($object->$value)) {
                        $find = true;
                    }
                }
                if (!$find) {
                    unset($data[$c]);
                }
            }
        }
        if ($input and $type == 'boolean') {
            foreach ($data as $c => $element) {
                if ($element->$key != ($input == 'true')) {
                    unset($data[$c]);
                }
            }
        }
        if ($input and $type == 'substring') {
            foreach ($data as $c => $element) {
                if (strpos(strtolower($element->$key), strtolower($input)) === false) {
                    unset($data[$c]);
                }
            }
        }
        if ($input and $type == 'date') {
            $compare = $filter['compare'];
            foreach ($data as $c => $element) {
                if ($compare == 'earlier' and strtotime($element->$key) > strtotime($input)) {
                    unset($data[$c]);
                }
                if ($compare == 'later' and strtotime($element->$key) < strtotime($input)) {
                    unset($data[$c]);
                }
            }
        }
    }
}
