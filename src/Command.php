<?php
namespace Offworks\Wizard;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->handle(new Arguments($input), new Options($input));
    }

    public function configure()
    {
        $this->setup();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Configure the console details
     * Alias to configure()
     * @return mixed
     */
    abstract public function setup();

    /**
     * Handle the command execution
     * use $this->app to get the application instance
     * For more information visit http://symfony.com/doc/current/console.html
     * @param Arguments $arguments
     * @param Options $options
     * @return
     */
    abstract public function handle(Arguments $arguments, Options $options);

    /**
     * @param $message
     * @param bool $newline
     * @param int $options
     * @return mixed
     */
    public function write($message, $newline = false, $options = 0)
    {
        return $this->output->write($message, $newline, $options);
    }

    /**
     * @param $message
     * @param int $options
     * @return mixed
     */
    public function writeLn($message, $options = 0)
    {
        return $this->output->writeln($message, $options);
    }

    /**
     * @param $question
     * @param null $default
     * @return mixed
     */
    public function ask($question, $default = null)
    {
        if(is_object($question) && $question instanceof Question)
        {
            $helper = $this->getHelper('question');

            return $helper->ask($this->input, $this->output, $question);
        }

        $question = new Question($question, $default);

        return $this->ask($question);
    }

    /**
     * As multiple question
     * @param array|Question[] $questions
     * @return array
     */
    protected function askMultiple(array $questions)
    {
        $answers = array();

        foreach($questions as $key => $question)
            $answers[$key] = $this->ask($question);

        return $answers;
    }

    /**
     * A ConfirmationQuestion
     * @param $message
     * @param bool $default
     * @return mixed
     */
    public function confirm($message, $default = true)
    {
        $question = new ConfirmationQuestion($message, $default);

        return $this->ask($question);
    }

    /**
     * A ChoiceQuestion
     * @param $message
     * @param array $choices
     * @param null $default
     * @return mixed
     */
    public function choose($message, array $choices, $default = null)
    {
        $question = new ChoiceQuestion($message, $choices, $default);

        return $this->ask($question);
    }

    /**
     * @param $message
     * @param null $key
     * @param array $data
     * @param bool $structureChangable
     * @param int $depth
     * @return bool|float|int|mixed|string
     */
    public function configureArray($message, $key = null, array $data, $structureChangable = false, $depth = 0)
    {
        $type = gettype($data);

        if($type == 'scalar')
            return $this->ask($message);

        $choices = array();
        $types = array();

        foreach ($data as $answer => $value) {
            $types[$answer] = gettype($value);

            if ($types[$answer] == 'array') {
                $choices[$answer] = '<info>' . $answer . '</info> array';
            }
            else {
                $value = $types[$answer] == 'boolean' ? ($value ? 'true' : 'false') : $value;
                $choices[$answer] = '<info>' . $answer . '</info> ' . '<comment>' . $value . '</comment>';
            }
        }

        $choices['save'] = array(
            'description' => 'save',
            'option' => 'y'
        );

        if ($structureChangable)
            $choices['change_type'] = array(
                'description' => 'change type',
                'option' => 'c'
            );

        $answer = $this->simplyChoose($message, $choices, 1);

        if ($answer == 'save')
            return $data;

        if ($answer == 'change_type') {
            $changedType = $this->simplyChoose('Change type for [' . $key . '] : ', array(
                'array' => 'array',
                'scalar' => 'scalar',
            ), 1);
        }

        $absoluteKey = $key ? $key . '.' . $answer : $answer;

        $answerType = $types[$answer] == 'integer' ? 'float' : $types[$answer];

        if($types[$answer] == 'array')
        {
            $data[$answer] = $this->configureArray('Set <info>' . $absoluteKey . '</info> : ', $absoluteKey, $data[$answer], $structureChangable, $depth + 1);
        }
        else
        {
            if($types[$answer] == 'boolean')
            {
                $data[$answer] = $this->simplyChoose('Set <info>' . $absoluteKey . '</info> [<comment>' . ($data[$answer] ? 'true' : 'false') . '</comment>] : ', array(
                    'true' => 'true',
                    'false' => 'false'
                ), 1) === 'true' ? true : false;
            }
            else
            {
                $data[$answer] = $this->askLn('Set <info>' . $absoluteKey . '</info> [<comment>' . $data[$answer] .'</comment>] : ');
                settype($data[$answer], $answerType);
            }
        }

        return $this->configureArray($message, $key, $data);
    }

    public function askLn($question, $default = null)
    {
        $this->writeLn($question);
        return $this->ask(' > ', $default);
    }

    /**
     * Make a assoc choice question, except the selection is by number
     * @param $message
     * @param array $choices
     * @param int $startIndex
     * @return mixed
     * @throws \Exception
     */
    public function simplyChoose($message, array $choices, $startIndex = 0)
    {
        $simpleChoices = array();

        $values = array();

        $index = $startIndex;

        foreach($choices as $value => $description)
        {
            if(is_array($description))
            {
                if(!isset($description['description']))
                    throw new \Exception('[description] of the option is missing.');

                if(isset($description['option']))
                {
                    $idx = $description['option'];
                }
                else
                {
                    $idx = $index;
                    $index++;
                }

                $values[$idx] = isset($description['value']) ? $description['value'] : $value;
                $simpleChoices[$idx] = $description['description'];
            }
            else
            {
                $values[$index] = $value;
                $simpleChoices[$index] = $description;
                $index++;

            }
        }

        $question = new AssocChoiceQuestion($message, $simpleChoices);

        $answer = $values[$this->ask($question)];

        return $answer;
    }
}