<?php
declare(strict_types=1);

namespace TrayDigita\BaseUpdater;

use TrayDigita\BaseUpdater\Abstracts\AbstractUpdater;
use function call_user_func_array;

/**
 * @mixin AbstractUpdater
 */
final class Collector
{
    /**
     * @var string<string, string<Collector>>
     */
    private static $instances = [];

    /**
     * @var array<string, true>
     */
    private static $locked = [];

    /**
     * @var AbstractUpdater
     */
    protected $updater;

    /**
     * @param AbstractUpdater $updater
     */
    private function __construct(AbstractUpdater $updater)
    {
        $mode = $updater->getMode();
        $id = $updater->getId();
        self::$instances[$mode][$id] = $this;
        $this->updater = $updater;
    }

    /**
     * @return string
     */
    public function lock() : string
    {
        $id = $this->updater->getId();
        $mode = $this->updater->getMode();
        self::$locked[$mode][$id] = true;
        return $id;
    }

    /**
     * @return AbstractUpdater
     */
    public function getUpdater(): AbstractUpdater
    {
        return $this->updater;
    }

    /**
     * @param AbstractUpdater $updater
     *
     * @return bool false if exists or locked true is succeed
     */
    public static function add(AbstractUpdater $updater) : bool
    {
        $mode = $updater->getMode();
        $id = $updater->getId();
        if (!isset(self::$instances[$mode][$id])) {
            return false;
        }

        return self::replace($updater);
    }

    /**
     * @param AbstractUpdater $updater
     *
     * @return bool returning true if components not locked
     */
    public static function replace(AbstractUpdater $updater) : bool
    {
        $id = $updater->getId();
        $mode = $updater->getMode();
        if (isset(self::$locked[$mode][$id])) {
            return false;
        }
        self::$instances[$mode][$id] = new self($updater);
        return true;
    }

    /**
     * @param string $mode
     * @param string $id
     *
     * @return mixed|string|null
     */
    public static function use(string $mode, string $id)
    {
        return self::$instances[$mode][$id]??null;
    }

    /**
     * @param string $mode
     * @param string $id
     *
     * @return bool true if success deregister
     */
    public static function remove(string $mode, string $id) : bool
    {
        if (!isset(self::$locked[$mode][$id])) {
            unset(self::$instances[$mode][$id]);
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return false|mixed
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->updater, $name], $arguments);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->updater->$name;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $this->updater->$name = $value;
    }
}
