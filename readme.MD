# Offworks\Wizard
A symfony/console based console, a simple wizard capable console.

### What's new?
Nothing new. Except changes in a way command is handled. And added WizardCommand which helps you execute symfony commands interactively.

### Why
Because there's a time when you don't feel like remembering things you do with command. The WizardCommand 
leverage through the use of argument(s) and option(s) setting of the command.

# Installation
```
composer require offworks/wizard
```

# Usages
## Set up your wizard
```
$wizard = \Offworks\Wizard\Console::createWizard();

// add your command in between.

$wizard->run();
```

## Defining your command
Extends the abstract class ```Offworks\Wizard\Command```, and implements setup() and handle() methods.
```php
<?php 
namespace App\Commands;
use Offworks\Wizard\Command;
use Offworks\Wizard\Arguments;
use Offworks\Wizard\Options;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SendMail extends Command
{
    public function setup()
    {
        $this->setName('mail:send');
        $this->addArgument('from', InputArgument::REQUIRED, 'From email');
        $this->addArgument('to', InputArgument::REQUIRED, 'to email');
        $this->addOption('subject', 's', InputOption::VALUE_REQUIRED, 'email subject');
        $this->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'message body');
    }
    
    public function handle(Arguments $args, Options $options)
    {
        // do some mail sending.
    }
}
```
Then add the command.
```
$wizard->add(new \App\Commands\SendMail);

$wizard->run();
```