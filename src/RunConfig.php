<?php

declare(strict_types=1);

namespace LTS\MarkdownTools;

final class RunConfig
{
    public function __construct(
        private string $pathToChapters,
        private ?string $cachePath = null
    ) {
    }

    public function getCachePath(): ?string
    {
        return $this->cachePath;
    }

    public function getPathToChapters(): string
    {
        return $this->pathToChapters;
    }
}
