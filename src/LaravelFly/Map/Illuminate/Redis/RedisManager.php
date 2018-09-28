<?php
namespace LaravelFly\Map\Illuminate\Redis;

use LaravelFly\Map\Illuminate\Database\ConnectionsTrait;

class RedisManager extends  \Illuminate\Redis\RedisManager
{
    use ConnectionsTrait;

    /**
     *
     * [
     *      cid => [name1 => conn1, name2 => conn2 ]
     * ]
     *
     * @var array $connections
     */
    protected $connections = [];


    public function __construct($driver, array $config)
    {
        parent::__construct($driver,$config);

        $this->initConnections(app('config')['database.redis']);

    }

    public function connection($name = null)
    {
        $name = $name ?: 'default';

        $cid = \Swoole\Coroutine::getuid();

        if (!isset($this->connections[$cid][$name])) {

            return $this->connections[$cid][$name] = $this->pools[$name]->get();
        }

        return $this->connections[$cid][$name];

    }

    function makeOneConn($name)
    {
        return  $this->resolve($name);
    }


    public function connections()
    {
        return $this->connections[\Swoole\Coroutine::getuid()];
    }
}