<?php
namespace Offworks\Wizard;

use Symfony\Component\Console\Input\InputDefinition;

class WizardInput extends \Symfony\Component\Console\Input\ArrayInput
{
    public function __construct(InputDefinition $definition)
    {
        try {
            parent::__construct(array(), $definition);
        } catch (\Exception $exception) {
        }
    }

    public function bind(InputDefinition $definition)
    {
        $this->definition = $definition;

        $this->parse();
    }
}