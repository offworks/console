<?php
namespace Offworks\Wizard;

/**
 * Class Question
 * Chainable question
 * @package Offworks\Wizard
 */
class Question
{
    /**
     * @var Command
     */
    private $command;

    public function __construct(Command $command, \Symfony\Component\Console\Question\Question $question)
    {
        $this->command = $command;

        $this->question = $question;
    }

    public function setValidator(callable $callback)
    {
        $this->question->setValidator($callback);

        return $this;
    }

    public function setHidden($hidden)
    {
        $this->question->setHidden($hidden);

        return $this;
    }

    public function ask()
    {
        return $this->command->ask($this->question);
    }
}