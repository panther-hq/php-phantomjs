<?php


namespace PhantomJs\Cache;


interface CacheInterface
{
    public function save(string $id, string $data): void;

    public function getPath(string $id): string;

    public function fetch(string $id): string;

    public function delete(string $id): void;

    public function exists(string $id): bool;
}
