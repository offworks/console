<?php
namespace Offworks\Wizard;

use Symfony\Component\Console\Input\InputInterface;

class Arguments
{
    /**
     * @var array
     */
    private $arguments;

    public function __construct(InputInterface $input)
    {
        $this->arguments = $input->getArguments();
    }

    /**
     * Get arguments
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->arguments[$name] : $default;
    }

    /**
     * Set argument
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->arguments[$name] = $value;
    }

    /**
     * Check whether argument exists
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->arguments[$name]);
    }
}