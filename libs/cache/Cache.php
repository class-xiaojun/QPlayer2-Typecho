<?php


namespace QPlayer\Cache;

use Exception;

abstract class Cache
{
    protected $prefix = 'QPlayer2_';

    public abstract function set($key, $data, $expire = 86400);
    public abstract function get($key);

    public function install() {}
    public function uninstall() {}

    public function flush() {
        $this->uninstall();
    }

    /**
     * @throws Exception
     */
    public function test() {
        $id = uniqid();
        $this->set('test', $id, 60);
        if ($this->get('test') != $id) {
            throw new Exception('Cache test error!');
        }
    }

    protected function getKey($key) {
        return $this->prefix . md5($key);
    }

    /**
     * @param string $type
     * @param string $host
     * @param int $port
     * @return Cache
     * @throws Exception
     */
    public static function Build($type, $host, $port)
    {
        $type = ucfirst($type);
        if (!in_array($type, array('Database', 'Memcached', 'Redis'))) {
            throw new Exception("Cache type error: $type");
        }
        include_once("$type.php");
        $type = 'QPlayer\Cache\\' . $type;
        return new $type($host, $port);
    }

    public static function BuildWithPlugin($plugin) {
        return self::Build($plugin->cacheType, $plugin->cacheHost, $plugin->cachePort);
    }
}