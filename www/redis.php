<?php

try {
    $redis = new Redis();
    $redis->connect('redis',6379);
    $redis->set('test','hello world!');
    echo $redis->get('test');
} catch (Exception $e) {
    echo $e->getMessage();
}