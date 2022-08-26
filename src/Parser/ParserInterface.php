<?php


namespace PhantomJs\Parser;


interface ParserInterface
{
    public function parse(string $data): array;
}
