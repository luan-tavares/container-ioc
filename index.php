<?php

require "vendor/autoload.php";

use App\A;
use App\C1;
use App\C2;
use Container\Container;
use App\E;
use App\InterfaceC;

Container::bind("c", function () {
    return Container::get(InterfaceC::class);
});

//Container::bind(InterfaceC::class, C1::class);
Container::singleton(InterfaceC::class, C2::class);

Container::singleton("fooA", "App\\SubA");

Container::bind("z", E::class);

Container::bind(E::class, A::class);

Container::bind("k", function () {
    return Container::get("fooA");
});


//echo Container::get("c") . "\n";
//echo Container::get("c") . "\n";
//$app = Container::get("fooA");


dd(json_encode(Container::getAll(), JSON_PRETTY_PRINT));