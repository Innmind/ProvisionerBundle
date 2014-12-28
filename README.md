# InnmindProvisionerBundle

[![Build Status](https://travis-ci.org/Baptouuuu/InnmindProvisionerBundle.svg?branch=master)](https://travis-ci.org/Baptouuuu/InnmindProvisionerBundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d1f6a225-90d5-47fd-a30a-cea46dd18fd4/big.png)](https://insight.sensiolabs.com/projects/d1f6a225-90d5-47fd-a30a-cea46dd18fd4)

This bundle provide a mechanism to automatically run symfony commands when one finishes and adapt the number runned based on the server resources available. Currently it only works for the `rabbitmq:consumer` command provided by [`RabbitMqBundle`](https://github.com/videlalvaro/RabbitMqBundle), but check the [documentation](Resources/doc/) to see to extend the bundle capacities.

The bundle also provide an laerting mechanism when server at full capacity or under used.

## Documentation

The whole [documentation](Resources/doc/README.md) is available in the `Resources/doc` folder.

## License

This bundle is under the MIT license. See the complete license in the bundle:

```
Resources/meta/LIENSE
```
