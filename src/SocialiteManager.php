<?php

namespace Socialite\SocialiteManager;

use Closure;
use InvalidArgumentException;
use Socialite\SocialiteManager\Contracts\FactoryInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

class SocialiteManager implements FactoryInterface
{
    use SocialiteConfigTrait;

    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * SocialiteManager Construct.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setConfig(new Config($config));
    }

    /**
     * Set the request instance.
     *
     * @param SymfonyRequest $request
     *
     * @return $this
     */
    public function setRequest(SymfonyRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Return the request instance.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            $this->request = $this->createDefaultRequest();
        }

        return $this->request;
    }

    /**
     * Get a driver instance.
     *
     * @param string $driver
     *
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Get a driver instance.
     *
     * @param string $driver
     *
     * @return mixed
     */
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (!isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    protected function createDriver($driver)
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $socialiteConfig = $this->getSocialiteConfig($driver);

        if (empty($socialiteConfig)) {
            throw new InvalidArgumentException("Driver [$driver] credentials are not provided.");
        }

        $providerClass = array_get($socialiteConfig, 'provider');
        $this->classExists($providerClass);
        $this->classExtends($providerClass, AbstractProvider::class);

        return $this->buildProvider($providerClass, $socialiteConfig);
    }

    /**
     * Detects whether the specified class exists.
     *
     * @param string $providerClass
     *
     * @throws InvalidArgumentException
     */
    private function classExists($providerClass)
    {
        if (!class_exists($providerClass)) {
            throw new InvalidArgumentException("$providerClass doesn't exist.");
        }
    }

    /**
     * Whether it is an extension class.
     *
     * @param string $class
     * @param string $baseClass
     *
     * @throws InvalidArgumentException
     */
    protected function classExtends($class, $baseClass)
    {
        if (!is_subclass_of($class, $baseClass)) {
            throw new InvalidArgumentException($class.' does not extend '.$baseClass);
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param string $driver
     *
     * @return mixed
     */
    protected function callCustomCreator($driver)
    {
        return $this->customCreators[$driver]();
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string  $driver
     * @param Closure $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get all of the created "drivers".
     *
     * @return array
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Create default request instance.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createDefaultRequest()
    {
        $request = SymfonyRequest::createFromGlobals();
        $session = new SymfonySession();
        $request->setSession($session);

        return $request;
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param string $provider
     * @param array  $config
     *
     * @return \Socialite\SocialiteManager\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->getRequest(), $config['client_id'],
            $config['client_secret'], $config['redirect']
        );
    }

    /**
     * Get the default driver name.
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }
}
