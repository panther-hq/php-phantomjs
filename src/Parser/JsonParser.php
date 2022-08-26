<?php


namespace PhantomJs\Parser;


class JsonParser implements ParserInterface
{

    public function parse(string $data): array
    {
        return json_decode($data, true);
    }
}
