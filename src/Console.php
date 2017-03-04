<?php
namespace Offworks\Wizard;

use Offworks\Wizard\Commands\WizardCommand;
use Symfony\Component\Console\Application;

class Console extends Application
{
    /**
     * Create a console application
     * with default WizardCommand added
     * @return static
     */
    public static function createWizard()
    {
        $console = new static();

        $console->add($command = new WizardCommand);

        foreach(array('list', 'help', 'wizard') as $name)
            $console->get($name)->setHidden(true);

        $console->setDefaultCommand($command->getName(), false);

        return $console;
    }
}