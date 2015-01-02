# Behaviour

The overall mechanism is pretty simple and is done in the following steps:

* a symfony command terminates its job
* the provisioner launch itself (if the command is one of the triggers from the config)
* it checks how many processes should be run
* it check how many processes can be run
* it runs the number of commands that can be run
* alerts are raised if thresholds are reached

## Provisioner bootstrap

It can automatically start itself by simply listening on the symfony `console.terminate` event.

If the command exit code is different from `0`, it won't start itself as you don't want a bunch of failing processes launching themselves on your servers.

And of course it won't start ever if the command name is not set the `triggers` config.

## Provisionning computing

The provisioner dispatch an event to let any listener decide, with its own logic, how many processes should be run.

Then the provisioner check on the server how many of the same commands are running and evaluate their CPU footprint. And it will do simple math to calculate how many processes can be run before reaching the CPU threshold.

Finally, it will run the number that can be run. For example, it 4 processes must be run but only 1 should be before reaching a threshold, it will only run 1.

## Alerting

In the end, it checks the current status of the server. If the server is under the minimum thresholds and no new commands needs to be run it will alert you (mail or webhook) that you may no longer need the server as it doesn't do much work anymore.

In the opposite, if your server is over the max thresholds and commands couldn't be run, it will alert you that you may need to set up new servers to handle the load.

### Email

Here are the templates of the mail sent:

```
[Provision alert] Server under used
```
```
Command: symfony:command:namespace
Command input: command:ns arg1 argN --opt1=x --optN=n
CPU usage: percent
Load average: float
```

```
[Provision alert] Server over used
```
```
Command: symfony:command:namespace
Command input: command:ns arg1 argN --opt1=x --optN=n
CPU usage: percent
Load average: float
Processes required: number of commands that couldn't be run
Processes running: number of commands running on the server
```

### Webhook

An alert correspond to a HTTP `POST` request issued to each of the URIs set in the config, the data posted follow this structure:

```
[
    'type' => 'under_used || over_used',
    'command' => 'symfony:command:namespace arg1 argN --opt1=x --optN=n',
    'cpu' => 'percent',
    'load_average' => 'float',
    'required_processes' => 'number of commands that couldn't be run',
    'running_processes' => 'number of commands running on the server'
]
```

### HipChat & Slack

Here are in the order the messages formats when over/under used alerts are raised.

```
Server at full capacity! Command: %s | CPU: %s | Load: %s | Required: %s | Running: %s
```

```
Server under used. You may take it down! Command: %s | CPU: %s | Load: %s
```
