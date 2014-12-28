# RabbitMQ

As said in the bundle description, by default this bundle only helps provisionning rabbitmq consumers. To do so, it relies on the [`RabbitMqBundle`](https://github.com/videlalvaro/RabbitMqBundle) and the `rabbitmqadmin` shell command.

In order to keep the bundle configuration simple, it automatically extract your RabbitMQ server configuration out of the `RabbitMqBundle` services. It extract the `host`, `user`, `password`, `port` and `vhost` for each of your consumers.

**Important**: as the `port` used by a consumer is different as the one used by `rabbitmqadmin`, the bundle *guess* the appropriate one. By default, the default admin port is `10000` above the one used by consumers, and so the bundle follows the same rule to *guess* ports. It means that if your consumer is configured to use `4242` port, the associated admin port must be `14242`.

## Behaviour

The only thing related to RammitMQ in this bundle is the part of deciding how many consumers should be run. It can do so by checking how many messages are in the queue.

**Important**: in order to work, you must launch your consumers by specifying the `messages` option (ie: `console rabbitmq:consumer consumerName --messages=X`)

In the order, it does the following:

* check how many consumers, with the same `consumerName`, are launched
* check how many messages one consumer handle
* check of many messages are in the queue
* retrieve the previous queue depths
* do a linear regression of the depth history, in order to predict based on the tendency (to avoid episodic peaks and falls)
    * if you don't want this behaviour, reduce the `rabbitmq.queue_depth.history_length` config to its minimum (which is `1`)
* compute how many consumers must be run
    * if the depth tendency decrease, run `1` or no consumers
    * if the depth tendency is flat, run `2` consumersto check how it behaves (and it will adjust on the next provisioner run)
    * if the depth tendency increase, run `depth / messagesHandledByOneConsumer` consumers

We end up with a mechanism fairly simple that does the job.

And in case no consumers are running, you'll automatically receive an alert to let you know. No longer need to check what's happening on your server.
