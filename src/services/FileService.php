<?php

namespace App\services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FileService {

    public function __construct(
        private Filesystem $filesystem = new Filesystem(),
    ) {}

    public function createAFileWithContent(string $filename, mixed $content): void {
        $this->filesystem->dumpFile("data/$filename", $content);
    }

    public function readFiles(string $regexFileName): Finder {
        $finder = new Finder();
        $finder->in('data');
        return $finder->files()->name($regexFileName);
    }
}