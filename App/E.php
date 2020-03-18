<?php

namespace App;

class E
{
    public function __toString()
    {
        return "[ Classe E #" . spl_object_id($this) . " instanciada ] \n";
    }

    public function executa()
    {
        echo "oi";
    }
}