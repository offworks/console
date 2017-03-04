<?php
namespace Offworks\Wizard\Commands;

use Offworks\Wizard\Arguments;
use Offworks\Wizard\ArrayChoiceQuestion;
use Offworks\Wizard\ArrayInput;
use Offworks\Wizard\AssocChoiceQuestion;
use Offworks\Wizard\Command;
use Offworks\Wizard\Options;
use Offworks\Wizard\WizardInput;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Question\Question;

class WizardCommand extends Command
{
    /**
     * Configure the console details
     * Alias to configure()
     * @return mixed
     */
    public function setup()
    {
        $this->setName('wizard');
        $this->setDescription('Access the wizard');
        $this->addOption('command', 'c', InputOption::VALUE_OPTIONAL);
        $this->addOption('search', 's', InputOption::VALUE_OPTIONAL);
    }
    
    /**
     * Handle the command execution
     * use $this->app to get the application instance
     * @param Arguments $args
     * @param Options $options
     */
    public function handle(Arguments $args, Options $options)
    {
        $this->output->getFormatter()->setStyle('magneto', new OutputFormatterStyle('blue', null, array('underscore')));

        $app = $this->getApplication();

        /** @var Command[] $commands */
        $commands = $app->all();

        if (!$options->has('command')) {
            INDEX:
            $choices = array();

            foreach ($commands as $command) {
                if (!$command->isHidden() && $command->isEnabled())
                    $choices[$command->getName()] = '<comment>' . $command->getName() . '</comment> ' . $command->getDescription();
            }

            $name = $this->simplyChoose('<info>Select your command :</info>', $choices, 1);
        } else {
            $name = $options->get('command');
        }

        $command = $app->find($name);
        $this->writeLn('<comment>Command : ' . $name . '</comment>');
        $definition = $command->getDefinition();
        $input = new WizardInput($definition);
        $this->configureArguments($command, $input, $definition);
        $this->configureOptions($command, $input, $definition);
        $command->run($input, new ConsoleOutput());
    }

    protected function configureArguments(SymfonyCommand $command, WizardInput $input, InputDefinition $definition)
    {
        START:
        $lastAnswer = false;
        foreach ($definition->getArguments() as $argument) {
            if($lastAnswer !== false && is_null($lastAnswer))
                break;

            $this->writeLn('<info>' . $argument->getDescription() . '</info>');
            $question = new Question('Set argument [<comment>' . $argument->getName() . '</comment>] ' . (!$argument->isRequired() ? '(optional)' : '') . ' : ');
            $question->setValidator(function ($answer) use ($argument) {
                if ($argument->isRequired() && is_null($answer))
                    throw new \Exception('Required argument cannot be empty');

                return $answer;
            });

            if ($answer = $this->ask($question))
                $input->setArgument($argument->getName(), $answer);

            $lastAnswer = $answer;
        }
    }

    protected function configureOptions(SymfonyCommand $command, WizardInput $input, InputDefinition $definition)
    {
        START:
        $optionals = array();

        $optionsMap = array();
        foreach ($definition->getOptions() as $option) {
            if ($option->isValueRequired()) {
                $this->writeLn('<info>' . $option->getDescription() . '</info>');
                $question = new Question('Set option [<comment>' . $option->getName() . '</comment>] : ');
                $question->setValidator(function ($answer) {
                    if (is_null($answer))
                        throw new \Exception('Required option cannot be empty');

                    return $answer;
                });
                $input->setOption($option->getName(), $this->ask($question));
            } else {
                $optionals[$option->getName()] = '<magneto>' . $option->getName() . '</magneto> ' . $option->getDescription();
                $optionsMap[$option->getName()] = $option;
            }
        }

        if (count($optionals) > 0) {
            CONFIGURE:
            $optionals['submit'] = array(
                'option' => 'y',
                'description' => $this->getCurrentCommand($command, $input),
                'value' => ''
            );

            $value = $this->simplyChoose('<info>Configure optional options : [y to submit]</info>', $optionals, 1);

            if ($value != '') {
                /** @var InputOption $option */
                $option = $optionsMap[$value];

                if ($option->acceptValue()) {
                    $this->writeLn($option->getDescription());
                    $answer = $this->ask('Set [' . $option->getName() . '] : ');
                    $optionals[$value] = '<magneto>' . $option->getName() . '</magneto> ' . $option->getDescription() . ' <info>(' . $answer . ')</info>';
                } else {
                    if ($input->getOption($option->getName()) === true) {
                        $answer = false;
                        $optionals[$value] = '<magneto>' . $option->getName() . '</magneto> ' . $option->getDescription();
                    } else {
                        $answer = true;
                        $optionals[$value] = '<magneto>' . $option->getName() . '</magneto> ' . $option->getDescription() . ' <info>(enabled)</info>';
                    }
                }

                $input->setOption($option->getName(), $answer);

                goto CONFIGURE;
            }
        }
    }

    protected function getCurrentCommand(SymfonyCommand $command, InputInterface $input)
    {
        $commands = array('php', $_SERVER['PHP_SELF']);

        $commands[] = $command->getName();

        foreach ($input->getArguments() as $name => $value) {
            if(!$value)
                continue;

            $commands[] = strpos($value, ' ') == false ? '"' . $value . '"' : $value;
        }

        foreach($input->getOptions() as $name => $value) {
            if (!$value)
                continue;

            $option = $command->getDefinition()->getOption($name);
            $name = $option->getShortcut();
            $commands[] = '-' . $name;
            if ($option->acceptValue())
                $commands[] = strpos($value, ' ') !== false ? '"' . $value . '"' : $value;
        }

        return '<comment>' . implode(' ', $commands) . '</comment>';
    }
}