<?php

declare(strict_types=1);

namespace LTS\MarkdownTools\Test;

use InvalidArgumentException;
use LTS\MarkdownTools\Cache;
use LTS\MarkdownTools\Helper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class TestHelper
{
    public const VAR_PATH   = __DIR__ . '/../var/tests/';
    public const CACHE_PATH = __DIR__ . '/../var/tests-cache/';
    private static Cache $cache;

    public static function nuke(): void
    {
        exec('rm -rf ' . self::VAR_PATH);
    }

    public static function getCache(): Cache
    {
        return self::$cache ?? (self::$cache = new Cache(self::CACHE_PATH));
    }

    public static function createVarDir(string $createDir): void
    {
        self::assertDirInVarDir($createDir);
        if (!is_dir(filename: $createDir)) {
            \Safe\mkdir(pathname: $createDir, mode: 0777, recursive: true);
        }
    }

    public static function createTestFile(
        string $contents = '',
        string $filename = null,
        string $createInDir = self::VAR_PATH
    ): string {
        if ($filename === null) {
            $filename = debug_backtrace(options: 0, limit: 2)[1]['function'] . '.txt';
        }
        self::assertDirInVarDir($createInDir);
        self::createVarDir($createInDir);
        $path = "{$createInDir}/{$filename}";
        \Safe\file_put_contents(filename: $path, data: $contents);

        return $path;
    }

    /**
     * @throws \Safe\Exceptions\FilesystemException
     *
     * @return array<string,string>
     */
    public static function getFilesContents(string $dir): array
    {
        $dir       = Helper::resolveRelativePath($dir);
        $keyOffset = strlen(string: Helper::resolveRelativePath(self::VAR_PATH));
        $return    = [];
        $iterator  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $path => $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }
            $key          = substr(string: $path, offset: $keyOffset);
            $return[$key] = \Safe\file_get_contents($path);
        }

        return $return;
    }

    private static function assertDirInVarDir(string $dir): void
    {
        if (str_starts_with(haystack: $dir, needle: '/') === false) {
            throw new InvalidArgumentException('invalid directory ' . $dir . ', must start with /');
        }
        $varPath = Helper::resolveRelativePath(relativePath: self::VAR_PATH);
        $dir     = Helper::resolveRelativePath(relativePath: $dir);
        if (str_starts_with(haystack: $dir, needle: $varPath) === false) {
            throw new InvalidArgumentException(
                'invalid directory, ' . $dir .
                ', must be sub dir of ' . $varPath
            );
        }
    }
}
