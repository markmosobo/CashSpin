<?php
/**
 * Environment Configuration Loader
 * 
 * This file loads environment variables from a .env file for the Cash & Spin application
 */

class EnvLoader {
    /**
     * The directory where the .env file can be located
     * 
     * @var string
     */
    protected $path;

    /**
     * Initialize the loader with the path to .env file
     * 
     * @param string $path
     */
    public function __construct($path = null) {
        $this->path = $path ?: dirname(__DIR__);
    }
    

    /**
     * Load environment variables from .env file
     * 
     * @return void
     */
    public function load() {
        $envFile = $this->path . '/.env';

        if (!file_exists($envFile)) {
            throw new \Exception('.env file not found. Please create one based on .env.example');
        }

        // Read file line by line
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Process valid environment variables
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Remove quotes if present
                if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                    $value = substr($value, 1, -1);
                } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
                    $value = substr($value, 1, -1);
                }

                // Handle variable substitution like ${APP_URL}
                $value = $this->parseVariables($value);
                
                // Set in environment if not already set
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }
                
                if (!array_key_exists($name, $_SERVER)) {
                    $_SERVER[$name] = $value;
                }
                
                putenv("{$name}={$value}");
            }
        }
    }

    /**
     * Parse variables in values like ${APP_URL}/api/callback
     * 
     * @param string $value
     * @return string
     */
    protected function parseVariables($value) {
        if (preg_match_all('/\${([a-zA-Z0-9_]+)}/', $value, $matches)) {
            foreach ($matches[0] as $index => $placeholder) {
                $varName = $matches[1][$index];
                if (isset($_ENV[$varName])) {
                    $value = str_replace($placeholder, $_ENV[$varName], $value);
                } elseif (getenv($varName) !== false) {
                    $value = str_replace($placeholder, getenv($varName), $value);
                }
            }
        }
        
        return $value;
    }

    /**
     * Get an environment variable
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null) {
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
        }
        
        return $value;
    }
}
?>