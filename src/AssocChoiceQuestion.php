<?php
namespace Offworks\Wizard;

use Symfony\Component\Console\Question\ChoiceQuestion;

class AssocChoiceQuestion extends ChoiceQuestion
{
    public function isAssoc($array)
    {
        return true;
    }
}