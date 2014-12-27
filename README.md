# InnmindProvisionerBundle

[![Build Status](https://travis-ci.org/Baptouuuu/InnmindProvisionerBundle.svg?branch=develop)](https://travis-ci.org/Baptouuuu/InnmindProvisionerBundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d1f6a225-90d5-47fd-a30a-cea46dd18fd4/big.png)](https://insight.sensiolabs.com/projects/d1f6a225-90d5-47fd-a30a-cea46dd18fd4)

This bundle automatically launches rabbitmq consumers when one finish its job. It will determine how many consumers to launch based on resource you allow and the number of messages to consume from the queue.

When it can't launch a new consumer due to resource limitation (not enough CPU), it will alert you by mail that you may need to buy more servers.
