# Installation

## Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
$ composer require innmind/provisioner-bundle "~1"
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

## Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php` file of your project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Innmind\ProvisionerBundle\InnmindProvisionerBundle(),
        );

        // ...
    }

    // ...
}
```

## Step 3: Add the configuration

Finally add the bundle configuration in the appropriate `config` file (ie: `app/config/config.yml`, or any of the env specific ones).

```yaml
innmind_provisioner:
    threshold:                          # this section is optional and by default only use cpu ones
        cpu:                            # these are the default values
            max: 100                    # beware of this percentage as it can be higher than 100 on servers with multiple cores
            min: 0
        load_average:
            max: 100
            min: 0
    triggers:
        - 'rabbitmq:consumer'           # list of symfony commands that triggers the provisioner when one of them finishes
     alerting:
        email: it@company.tld           # the mail where to send alerts (optional)
        webhook:
            - 'http://url/to/webhook'   # URIs to notify when alerts raised (optional)
    rabbitmq:
        queue_depth:
            history_length: 10          # how many queue depth to keep, higher will improve prediction on the number of consumers to run; too high will slow down a bit the provisionning process. history is kept in files in symfony cache directory
```
