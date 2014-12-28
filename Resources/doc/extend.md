# Extending the Bundle

In this bundle, you can extend 2 things, which command to provision and the alerters.

## Add a command to provision

First step is of course to add the command namespace in the `triggers` config (of course you don't do this if you're not in an app context, meaning building a bundle).

For the example, say we want to provision the command named `vendor:cmd`.

```yaml
# app/config/config{_env}.yml
#...

innmind_provisioner:
    triggers:
        - 'vendor:cmd'
```

The next step is to build an event listener that listen to the event `innmind_provisioner.compute_requirements`.

```php
namespace Vendor\FooBundle\EventListener;

use Innmind\ProvisionerBundle\Event\ProvisionRequirementEvent;

class RequirementListener
{
    public function handle(ProvisionRequirementEvent $event)
    {
        if ($event->getCommandName() !== 'vendor:cmd') {
            //don't try to provision other commands
            return;
        }

        // build your logic to determine how many
        // commands to run

        $event->setRequiredProcesses($numberOfCommandsToRun);
        $event->stopPropagation();
    }
}
```

And register your listener as a service:

```yaml
# src/Vendor/FooBundle/Resources/config/services.yml

services:
    vendor_foo.listener.whatever:
        class: Vendor\FooBundle\EventListener\RequirementListener
        tags:
            - { name: kernel.event_listener, event: innmind_provisioner.compute_requirements, method: handle }
```

And voila! Now it will automatically provision `vendor:cmd` based on the requirements set by your listeners (and also benefit of the alerting system).

## Add an alerter

Say emails and webhooks are not enough for you, and you want to be notified on your favorite chat software, no worry! You can add your own channel to alert yourself.

First, create a class like this:
```php
namespace Vendor\FooBundle;

use Innmind\ProvisionerBundle\Alert\AlerterInterface;
use Symfony\Component\Console\Input\InputInterface;

class MyAlerter implements AlerterInterface
{
    public function alert($type, $name, InputInterface $input, $cpuUsage, $loadAverage, $leftOver = 0)
    {
        //$type can be self::UNDER_USED or self::OVER_USED
        //$name is the command namespace ie 'vendor:cmd'
        //$input is the command input with all the set arguments and options
        //$cpuUsage and $loadAverage are self explanatory
        //$leftOver is the number of commands that couldn't be run
    }
}
```

In order to acknowledge the bundle of your alerter, specify it as a service like this:

```yaml
services:
    vendor_foo.my_alerter:
        class: Vendor\FooBundle\MyAlerter
        tags:
            - { name: innmind_provisioner.alerter }
```

And done! The bundle will check for all services tagged with `innmind_provisioner.alerter` and use them on each alerts.
