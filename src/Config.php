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
    private const CONFIG_FILE_NAME = 'laracash';

    /**
     * @var Repository
     */
    private $config;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $configPath = $this->configurationPath();

        $configFile = $configPath . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME . '.php';

        if (!file_exists($configFile)) {
            throw new Exception('Config file not found');
        }

        $this->config = new Repository(require $configFile);
    }

    private function configurationPath(): string
    {
        $configPath = __DIR__ . DIRECTORY_SEPARATOR . 'Config';

        if (function_exists('config_path')) {
            $configPath = config_path();
        }

        return $configPath;
    }

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->config->get($key);
    }
}
