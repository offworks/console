<?php
namespace Offworks\Wizard;

use Offworks\Wizard\Commands\WizardCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

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

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if($input->hasParameterOption(array('-w', '--wizard')))
        {
            $wizard = $this->get('wizard');
            $command = $this->get($this->getCommandName($input));
            $input = new WizardInput($wizard->getDefinition());
            $input->setArgument('cmd', $command->getName());
            return $wizard->run($input, new ConsoleOutput());
        }

        parent::doRun($input, $output);
    }
}