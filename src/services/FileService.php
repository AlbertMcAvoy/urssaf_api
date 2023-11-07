<?php

namespace App\services;

use Symfony\Component\Filesystem\Filesystem;

class FileService {

    public function __construct(
        private Filesystem $filesystem = new Filesystem()
    ) {}

    public function createAFileWithContent(string $filename, mixed $content): void {
        $this->filesystem->dumpFile("data/$filename", $content);
    }
}