<?php
declare(strict_types=1);

namespace PhantomJs\Cache;



use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

final class FileCache implements CacheInterface
{

    protected Filesystem $filesystem;


    public function __construct()
    {
        $adapter = new LocalFilesystemAdapter(sys_get_temp_dir());
        $this->filesystem = new Filesystem($adapter);
    }

    public function save(string $id, string $data): void
    {
        $this->filesystem->write($id, $data);
    }

    public function getPath(string $id): string
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.$id;
    }

    public function fetch(string $id): string
    {
        return $this->filesystem->read($id);
    }

    public function delete(string $id): void
    {
        $this->filesystem->delete($id);
    }

    public function exists(string $id): bool
    {
        return $this->filesystem->has($id);
    }


}