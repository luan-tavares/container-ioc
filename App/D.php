<?php

namespace App;

class D
{
    public function __toString()
    {
        return "[ Classe D #" . spl_object_id($this) . " instanciada ] \n";
    }
}