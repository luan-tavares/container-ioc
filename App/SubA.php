<?php

namespace App;

use App\A;
use App\B;
use App\D;

class SubA extends A
{
    public function __construct(B $b, D $d)
    {
        $this->b = $b;
        $this->d = $d;
    }

    public function __toString()
    {
        return $this->b . $this->d . "[ Classe SubA #" . spl_object_id($this) . " instanciada dependendo de B #" . spl_object_id($this->b) . " e D #" . spl_object_id($this->d) . " ] \n";
    }
}