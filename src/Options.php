<?php
namespace Offworks\Wizard;

use Symfony\Component\Console\Input\InputInterface;

class Options
{
    /**
     * @var array
     */
    private $options;

    public function __construct(InputInterface $input)
    {
        $this->options = $input->getOptions();
    }

    /**
     * Get an option
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->options[$name] : $default;
    }

    /**
     * Set an option
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Check whether option exists
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->options[$name]);
    }
}