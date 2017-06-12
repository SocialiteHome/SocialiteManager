<?php

namespace Socialite\SocialiteManager;

use Socialite\SocialiteManager\Contracts\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * Data associated with the object.
     *
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->config;
    }

    /**
     * Get a specific key value.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->config[$key];
        }

        return $default;
    }

    /**
     * Set a value to a key.
     *
     * @param $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        if (is_null($key)) {
            $this->config[] = $value;
        } else {
            $this->config[$key] = $value;
        }

        return $this;
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        if (array_key_exists($key, $this->config)) {
            return $key;
        }

        return false;
    }
}
