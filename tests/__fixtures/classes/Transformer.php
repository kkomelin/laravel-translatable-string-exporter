<?php

namespace Tests\__fixtures\classes;

class Transformer
{
    public static function staticMethod($s)
    {
        return \strtoupper(\str_replace(["-","_"], " ", $s));
    }

    public function publicMethod($s)
    {
        return \strtoupper(\str_replace(["-","_"], " ", $s));
    }
};
