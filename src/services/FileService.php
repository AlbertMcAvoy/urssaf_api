<?php

namespace App\services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FileService {

    public function __construct(
        private Filesystem $filesystem = new Filesystem(),
    ) {}

    public function createFileWithContent(string $filename, mixed $content): Finder {
        $this->filesystem->dumpFile("data/$filename", $content);
        return $this->readFiles($filename);
    }

    public function updateFileWithContent(string $filename, mixed $content): Finder {
        $this->filesystem->dumpFile("data/$filename", $content);
        return $this->readFiles($filename);
    }
    public function deleteFile(string $filename): void {
        $this->filesystem->remove("data/$filename");
    }


    public function readFiles(string $regexFileName): Finder {
        $finder = new Finder();
        $finder->in('data');
        return $finder->files()->name($regexFileName);
    }
}