<?php
namespace Offworks\Wizard\Commands;

use Offworks\Wizard\Arguments;
use Offworks\Wizard\Command;
use Offworks\Wizard\Options;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class TestCommand extends Command
{
    /**
     * Configure the console details
     * Alias to configure()
     * @return mixed
     */
    public function setup()
    {
        $this->setName('test');
        $this->setDescription('Just testing.');
        $this->addArgument('foo', InputArgument::REQUIRED, 'Fill in the foo like you re going to poop.');
        $this->addArgument('bar', InputArgument::OPTIONAL, 'This is just optional just in case.');
        $this->addArgument('baz', InputArgument::OPTIONAL, 'And another optional argument.');
        $this->addOption('yoyo', 'y', InputOption::VALUE_REQUIRED, 'require man.');
        $this->addOption('live', 'e', InputOption::VALUE_NONE, 'To live healthy');
        $this->addOption('die', 'm', InputOption::VALUE_NONE, 'To live sadly');
    }

    /**
     * Handle the command execution
     * use $this->app to get the application instance
     * For more information visit http://symfony.com/doc/current/console.html
     * @param Arguments $arguments
     * @param Options $options
     * @return
     */
    public function handle(Arguments $arguments, Options $options)
    {
    }
}