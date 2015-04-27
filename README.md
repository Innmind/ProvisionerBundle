# ProvisionerBundle

[![Build Status](https://travis-ci.org/Innmind/ProvisionerBundle.svg?branch=master)](https://travis-ci.org/Innmind/ProvisionerBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/ProvisionerBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/ProvisionerBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Innmind/ProvisionerBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/ProvisionerBundle/?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3e5c3b50-4489-4a73-a007-e5777d74d894/big.png)](https://insight.sensiolabs.com/projects/3e5c3b50-4489-4a73-a007-e5777d74d894)

This bundle provide a mechanism to automatically run symfony commands when one finishes and adapt the number runned based on the server resources available. Currently it only works for the `rabbitmq:consumer` command provided by [`RabbitMqBundle`](https://github.com/videlalvaro/RabbitMqBundle), but check the [documentation](Resources/doc/) to see to extend the bundle capacities.

The bundle also provide an laerting mechanism when server at full capacity or under used.

## Documentation

The whole [documentation](Resources/doc/README.md) is available in the `Resources/doc` folder.

## License

This bundle is under the MIT license. See the complete license in the bundle:

```
Resources/meta/LIENSE
```
