<?php

namespace Socialite\SocialiteManager\Contracts;

interface ConfigInterface
{
    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all();

    /**
     * Get a specific key value.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null);

    /**
     * Set a value to a key.
     *
     * @param $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set($key, $value);

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key);
}
