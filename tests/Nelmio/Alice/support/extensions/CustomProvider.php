<?php

namespace Nelmio\Alice\support\extensions;

class CustomProvider
{
    public function fooGenerator()
    {
        return 'foo';
    }

    public function randomNumber()
    {
        return mt_rand(0, 9);
    }

    public function noop($str)
    {
        return $str;
    }
}
