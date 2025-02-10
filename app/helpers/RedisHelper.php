<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    public static function get($key)
    {
        return Redis::get($key);
    }

    public static function set($key, $value)
    {
        Redis::set($key, $value);
    }

    public static function exists($key)
    {
        return Redis::exists($key);
    }

    public static function del($key)
    {
        $cursor = 0;
        do {
            $result = Redis::scan($cursor, ['MATCH' => $key . '*', 'COUNT' => 100]);

            if (!empty($result)) {
                list($cursor, $keys) = $result;

                if (!empty($keys) && is_array($keys)) {
                    foreach ($keys as $keyName) { // Renaming $key to $keyName to avoid conflicts
                        Redis::del($keyName);
                    }
                }
            }
        } while ($cursor != 0);
    }

    public static function keys($key)
    {
        return Redis::keys($key);
    }
}
