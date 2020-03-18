<?php

function vaaai($cliente) {

    return "oi" . $cliente;

}

if (!function_exists('dd')) {

    function dd($var) {

        print_r($var);
        die();
    }

}