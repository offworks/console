<?php
namespace Offworks\Wizard;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class Arguments
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var Command
     */
    protected $command;

    public function __construct(Command $command, InputInterface $input)
    {
        $this->command = $command;

        $this->input = $input;
    }

    /**
     * Get arguments
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->input->getArgument($name) : $default;
    }

    /**
     * Set argument
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->input->setArgument($name, $value);

        return $this;
    }

    /**
     * Check whether argument exists
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->input->hasArgument($name);
    }
}