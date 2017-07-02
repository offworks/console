<?php
namespace Offworks\Wizard;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class Options
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
     * Get an option
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->input->getOption($name) : $default;
    }

    /**
     * Set an option
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->input->setOption($name, $value);

        return $this;
    }

    /**
     * Check whether option exists
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        $options = array('--' . $name);

        if($shortcut = $this->command->getDefinition()->getOption($name)->getShortcut())
            $options[] = '-' . $shortcut;

        return $this->input->hasParameterOption($options);
    }
}