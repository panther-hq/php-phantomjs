<?php

namespace PhantomJs;

final class StringUtils
{
    public static function random(int $length = 20): string
    {
        return substr(md5(mt_rand()), 0, $length);
    }
}
