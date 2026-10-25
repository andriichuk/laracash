<?php

declare(strict_types=1);

namespace Andriichuk\Laracash;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as RepositoryContract;

/**
 * Class Config
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class Config
{
    private RepositoryContract $config;
    private string $prefix = '';

    /**
     * @throws Exception
     */
    public function __construct(?RepositoryContract $config = null)
    {
        $container = Container::getInstance();

        if ($config === null && $container->bound('config')) {
            /** @var RepositoryContract $config */
            $config = $container->make('config');
        }

        if ($config !== null) {
            $this->config = $config;
            $this->prefix = 'laracash.';

            return;
        }

        /** @var array $config */
        $config = require $this->configFile();
        $this->config = new Repository($config);
    }

    private function configFile(): string
    {
        $appConfigFile = $this->appConfigFilePath();

        if ($appConfigFile && file_exists($appConfigFile)) {
            return $appConfigFile;
        }

        $packageConfigFile = $this->packageConfigFilePath();

        if (file_exists($packageConfigFile)) {
            return $packageConfigFile;
        }

        throw new Exception('Configuration file for Laracash package not found');
    }

    private function appConfigFilePath(): string
    {
        if (function_exists('config_path') && method_exists(Container::getInstance(), 'configPath')) {
            return config_path($this->fileName());
        }

        return '';
    }

    private function packageConfigFilePath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . $this->fileName();
    }

    private function fileName(): string
    {
        return 'laracash.php';
    }

    public function get(string $key): mixed
    {
        return $this->config->get($this->prefix . $key);
    }
}
