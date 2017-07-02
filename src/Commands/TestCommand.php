<?php
namespace Offworks\Wizard\Commands;

use Offworks\Wizard\Arguments;
use Offworks\Wizard\Command;
use Offworks\Wizard\Options;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
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
        $this->setDefinition(new InputDefinition(array(
            new InputOption('hello', 'z', InputOption::VALUE_OPTIONAL, 'Test test'),
            new InputOption('hellor', null, InputOption::VALUE_NONE, 'Test test'),
            new InputOption('darta', 't', InputOption::VALUE_NONE, 'Test test')
        )));
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
//        $this->write($options->get('hello'));

        var_dump($this->configureArray('Configure this message :', null, array(
            'env' => 'dev',
            'is_double' => 10.5,
            'factories' => array(
                'configurable' => false,
                'session' => array(
                    'class' => 'My\\Session',
                    'dependencies' => 'ohho'
                ),
                'url' => array(
                    'class' => 'My\\UrlFactory',
                    'dependencies' => 'hehe'
                )
            ),
            'db' => array(
                'host' => 'localhost',
                'user' => 'root',
                'pass' => '',
                'name' => 'dorato'
            )
        )));
    }
}