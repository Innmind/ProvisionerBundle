# InnmindProvisionerBundle

This bundle automatically launches rabbitmq consumers when one finish its job. It will determine how many consumers to launch based on resource you allow and the number of messages to consume from the queue.

When it can't launch a new consumer due to resource limitation (not enough CPU), it will alert you by mail that you may need to buy more servers.
