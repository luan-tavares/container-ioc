<?php

namespace App;

use App\InterfaceC;

class C2 implements InterfaceC
{
    public function getName()
    {
        return "C2";
    }

    public function __toString()
    {
        return "[ Classe C2 #" . spl_object_id($this) . " instanciada ] \n";
    }
}