<?php

namespace Socialite\SocialiteManager;

use Socialite\SocialiteManager\Contracts\ConfigInterface;

trait SocialiteConfigTrait
{
    /**
     * The socialite configuration.
     *
     * @var Socialite\SocialiteManager\Contracts\ConfigInterface
     */
    protected $config;

    /**
     * Set the config.
     *
     * @param ConfigInterface $config
     *
     * @return $this
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the config.
     *
     * @return Socialite\SocialiteManager\Contracts\ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the socialite config.
     *
     * @param string $driver
     *
     * @return array
     */
    public function getSocialiteConfig($driver)
    {
        return $this->getConfig()->get($driver);
    }
}
