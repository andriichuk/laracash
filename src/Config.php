<?php

declare(strict_types=1);

namespace Andriichuk\Laracash;

use Exception;
use Illuminate\Config\Repository;

/**
 * Class Config
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class Config
{
    private Repository $config;

    /**
     * @throws Exception
     */
    public function __construct()
    {
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
        if (function_exists('config_path')) {
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
        return $this->config->get($key);
    }
}
