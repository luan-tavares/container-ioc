<?php

namespace App;

use App\InterfaceC;

class B
{
    protected $c;

    public function __construct(InterfaceC $c)
    {
        $this->c = $c;
    }

    public function __toString()
    {
        return $this->c . "[ Classe B #" . spl_object_id($this) . " instanciada dependendo de {$this->c->getName()} #" . spl_object_id($this->c) . " ] \n";
    }
}