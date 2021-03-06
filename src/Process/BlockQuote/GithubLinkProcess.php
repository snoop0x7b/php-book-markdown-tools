<?php

declare(strict_types=1);

namespace LTS\MarkdownTools\Process\BlockQuote;

use LTS\MarkdownTools\CachingUrlFetcher;

final class GithubLinkProcess implements BlockQuoteProcess
{
    private const URL_REGEXP = <<<'REGEXP'
%https://github.com/.+%
REGEXP;
    private LinkProcessor $linkProcessor;

    public function __construct(private CachingUrlFetcher $urlFetcher, ?LinkProcessor $linkProcessor = null)
    {
        $this->linkProcessor = $linkProcessor ?? new LinkProcessor(self::URL_REGEXP, $this->urlFetcher);
    }

    public function shouldProcess(string $blockquote): bool
    {
        return $this->linkProcessor->shouldProcess($blockquote);
    }

    public function processBlockQuote(string $blockquote): string
    {
        return $this->linkProcessor->processBlockQuote($blockquote);
    }
}
