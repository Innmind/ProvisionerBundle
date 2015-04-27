# Tuning the trigger process

Before trying to provision the command, the bundle us a voting mechanism to know if the whole process should be started for the command.

By default there is one voter granting to start the process if the command name is one of the specified in your configuration.
In many cases this is enough, but in some cases you may want a greater control on this.

Let's take an example where you want to provision the command `rabbitmq:consumer` but only the ones listening to the `image` channel (and not the `product` one).

In such case, you can simply create a voter as follows:

```php
use Innmind\ProvisionerBundle\Voter\VoterInterface;
use Symfony\Component\Console\Input\InputInterface;

class MyVoter implements VoterInterface
{
    public function supportsCommand($command)
    {
        return $command === 'rabbitmq:consumer';
    }

    public function vote($command, InputInterface $input)
    {
        $channel = $input->getArgument('name');

        switch ($channel) {
            case 'image':
                return self::TRIGGER_GRANTED;
            case 'product':
                return self::TRIGGER_DENIED;
            default:
                return self::TRIGGER_ABSTAIN;
        }
    }
}
```
Here, we grant when we try to provision the `image` channel, deny for the `product` one, otherwise we do not say anything. Of course you could deny for everything else than `image`.

**Note**: as you may have guessed, this voter won't be called if the bundle tries to provision a command different than `rabbitmq:consumer` (thanks to the `supportsCommand` method).

The last step is to register this voter in the bundle, and is done via defining this voter as a service and tagging it.

```yaml
services:
    my_voter:
        class: MyVoter
        tags:
            - { name: innmind_provisioner.voter }
```

If you leave the bundle configuration as is, this voter won't affect the provision process as the default voting strategy is to `affirmative` (and the default voter will automatically grant the process).

This strategy notion is the way you want the result of all the voters to be interpreted.

* `affirmative`: provision if at least one voter grant
* `consensus`: provision if more voters grants than denies
* `unanimous`: provision if all voters grants

In case of the `consensus` strategy, if there's an equality between grants and denies, you can decide by setting the config key `allow_if_equal_granted_denied` to `true` or `false`.

There's also a switch in case all voters abstain: `allow_if_all_abstain`.

This config keys are defined under the `trigger_manager` one:

```yaml
innmind_provisioner:
    #...
    trigger_manager:
        strategy: 'consensus'
        allow_if_equal_granted_denied: false
        allow_if_all_abstain: false
