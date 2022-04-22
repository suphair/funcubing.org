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

        /*

          $type = $filter['type'];
          $array = $filter['array'] ?? false;
          $value = $filter['value'] ?? false;
          if ($input and $array and $value) {
          foreach ($data as $c => $element) {
          $array = $filter['array'];
          $value = $filter['value'];
          $find = false;
          foreach ($element->$array ?? [] as $object) {
          if ($type == 'equal' and strtolower($input) == strtolower($object->$value)) {
          $find = true;
          }
          if ($type == 'substring' and strpos(strtolower($object->$value), strtolower($input)) === false) {
          $find = true;
          }
          if ($type == 'boolean' and ($object->$value ?? false) == ($input == 'true')) {
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
          if (($element->$key ?? false) != ($input == 'true')) {
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
          if ($input and $type == 'equal') {
          foreach ($data as $c => $element) {
          if (strtolower($element->$key) != strtolower($input)) {
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

         */
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
        if ($compare == 'later' and strtotime($element->$key) >= strtotime($input)) {
            return true;
        }
    }
    return false;
}
