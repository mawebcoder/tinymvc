<?php

namespace System\Providers;

use System\Helper\Helper;
use System\Exceptions\ConfigFileNotExistsException;
use System\Exceptions\CommandDirectoryNotFoundException;

class CommandServiceProvider
{

    private(set) readonly string $name;
    /**
     * @var string[]
     */
    public static array $commands = [];

    public array $files = [];

    /**
     * @throws ConfigFileNotExistsException
     * @throws CommandDirectoryNotFoundException
     */
    public function register(): void
    {
        if (!Helper::isRunningConsole()) {
            return;
        }
        $commandsPath = Helper::getConfig('app.commands_path');

        foreach ($commandsPath as $path) {
            $this->setPathClasses($path);
        }

        foreach ($this->files as $file) {
            $command = require $file;

            self::$commands[$command->signature] = get_class($command);
        }
    }

    /**
     * @throws CommandDirectoryNotFoundException
     */
    private function setPathClasses(string $path): void
    {
        $docs = scandir($path);

        if (!$docs) {
            throw new CommandDirectoryNotFoundException("{$path} is not a directory");
        }

        unset($docs[0], $docs[1]);

        foreach ($docs as $doc) {
            $docPath = $path . DIRECTORY_SEPARATOR . $doc;
            if (is_file($docPath)) {
                $this->files[] = $docPath;

                continue;
            }

            if (is_dir($docPath)) {
                $this->setPathClasses($docPath);
            }
        }
    }
}