<?php

namespace App\services;

use Symfony\Component\Filesystem\Filesystem;

class FileService {

    public function __construct(
        private Filesystem $filesystem = new Filesystem()
    ) {}

    public function createAFileWithContent(string $filename, mixed $content) {
        $this->filesystem->appendToFile("$filename", "$content;");
    }
}