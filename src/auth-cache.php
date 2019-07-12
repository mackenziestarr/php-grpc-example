<?php
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use Google\Auth\Cache\Item;


class YacItemPoolCache implements CacheItemPoolInterface {
    private $pool;
    public function __construct() {
        $this->pool = new Yac();
        // var_dump($this->pool->info());
    }
    public function getItem($key) {
        $result = $this->pool->get(md5($key));
        return $result ?: new Item($key);
    }
    public function save(CacheItemInterface $item): bool {
        return $this->pool->set(
            md5($item->getKey()),
            $item
        );
    }
    // not implemented
    public function hasItem($key) {throw new Exception();}
    public function getItems(array $keys = []) {throw new Exception();}
    public function saveDeferred(CacheItemInterface $item) {throw new Exception();}
    public function deleteItems(array $keys = []) {throw new Exception();}
    public function deleteItem($key) {throw new Exception();}
    public function commit() {throw new Exception();}
    public function clear() {throw new Exception();}
}