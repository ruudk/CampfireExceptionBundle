RuudkCampfireExceptionBundle
============================

A Symfony2 Bundle that logs exceptions to a Campfire chat room

## Installation

### Step1: Require the package with Composer

``php composer.phar require ruudk/campfire-exception-bundle``

### Step2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new Ruudk\CampfireExceptionBundle\RuudkCampfireExceptionBundle(),
    );
}
```

### Step3: Configure

Finally, add the following to your config_prod.yml

``` yaml
# app/config/config_prod.yml

ruudk_campfire_exception:
    application: # Your Symfony2 application name
    subdomain: # Subdomain
    token: # Campfire API token
    room: # Room ID
```

Congratulations! You're ready. 