<?php

namespace Reviz\FrontBundle\Zob;

use Sensio\FabioSalsa;

class Zob extends FabioSalsa implements FabioSamba {

    public function zobAction(Zob $zob) {
        $raphaelle = $this->fuck($zob);

        return $raphaelle;
    }
}