<?php

function find_element($array, $key, $value, $default) {
    foreach ($array ?? [] as $element) {
        if ($value === $element->$key) {
            return $element;
        }
    }

    return $default;
}
