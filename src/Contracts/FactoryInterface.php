<?php

namespace Socialite\SocialiteManager\Contracts;

interface FactoryInterface
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param string $driver
     *
     * @return mixed
     */
    public function driver($driver = null);
}
