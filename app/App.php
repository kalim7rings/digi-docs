<?php

namespace App;


class App {

    const SEPARATOR = '/[:\.]/';

    private static $instance = null;

    private $settings = [];

    private $slim = null;


    protected function __construct($settings = [])
    {
        $this->settings = $settings;

        $this->slim = new \Slim\App($settings);

    }

    final public static function instance($settings = [])
    {
        if (null === static::$instance) {
            static::$instance = new static($settings);
        }

        return static::$instance;
    }

    public function url($url = '', $includeBaseUrl = true)
    {
        $baseUrl = $includeBaseUrl ? $this->getConfig('settings.app.baseUrl') : '';

        if (strlen($url) > 0 && $url[0] == '/') {
            $url = ltrim($url, '/');
        }

        return ($baseUrl .  $url);
    }

    public function mix($path, $manifestDirectory)
    {
        static $manifests = [];

        $manifestPath = './public/mix-manifest.json';

        if (!isset($manifests[$manifestPath])) {
            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (!isset($manifest[$path])) {
            return $path;
        }

        return $manifestDirectory.$manifest[$path];
    }

    public function getConfig($string, $default = null)
    {
        $array = $this->settings;
        if ( !empty($string)) {
            $keys = preg_split(self::SEPARATOR, $string);
            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
        }

        return $array;
    }

    public function getContainer()
    {
        return $this->slim->getContainer();
    }

    public function add($middleware)
    {
        return $this->slim->add($middleware);
    }



    //proxy all gets to slim
    public function __get($name)
    {
        $c = $this->getContainer();

        if ($c->has($name)) {
            return $c->get($name);
        }
        return $this->resolve($name);
    }

    //proxy all sets to slim
    public function __set($k, $v)
    {
        $this->slim->{$k} = $v;
    }

    // proxy calls to slim
    public function __call($fn, $args = [])
    {
        if (method_exists($this->slim, $fn)) {
            return call_user_func_array([$this->slim, $fn], $args);
        }
        throw new \Exception('Method not found :: '.$fn);
    }
}