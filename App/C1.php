<?php

namespace App;

use App\InterfaceC;

class C1 implements InterfaceC
{
    public function getName()
    {
        return "C1";
    }

    public function __toString()
    {
        return "[ Classe C1 #" . spl_object_id($this) . " instanciada ] \n";
    }

    public function executa()
    {
        return "Você recebeu um email do container C1";
    }
}