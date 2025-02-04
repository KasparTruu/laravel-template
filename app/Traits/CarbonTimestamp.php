<?php

namespace App\Traits;

trait CarbonTimestamp
{
    #[\ReturnTypeWillChange]
    public function createFromTimestamp($timestamp, $tz = null): static
    {
        return parent::createFromTimestamp($timestamp, $tz);
    }
}