# {{ title }}{.text-success}
<hr>

This project was designed to be really easy to install and mainly to be updated with
your awesome content. We decided to not include a database persistence because of this, the simple
structure we intent to keep here.

### Require `CmThizer` via composer

`composer require thizer/cmthizer`

--------

## Step by step...

### There's two ways to install `CmThizer`:

> The first one is downloading it as it is and simple modify, add and delete this existing
> documents. You may note that like this, your project will not be synchronized with `CmThizer`
> repository so you will lost future updates, bug fixes and whatever...
> 
> By this way, run `php composer.phar update` after download it.

##### **Recommended:**{.mb-0}
> The second and **recommended** is use it as a lib so you will keep updating with
> `composer update`. Your project will be always synchronized with latest version of
> `CmThizer` and you will never lost a thing.

### Assuming you will use it as recommended <i class="fa fa-smile-o"></i>

For first create the folder for your site and start a [composer](https://getcomposer.org/doc/01-basic-usage.md) project.
Add `thizer/cmthizer` as a dependency and run `php composer.phar install`. Your `composer.json` file will look like this:

```json
{
  "name" : "thegreat/awesome-project",
  "description" : "Awesome Site",
  "license" : "private",
  "keywords" : ["site"],
  "homepage" : "https://awesomesite.com",
  "require" : {
      "php" : ">=7.0",
      "thizer/cmthizer" : "dev-master"
  }
}

```

* `CmThizer` require at least PHP 7.0 to properly work.

As you probally know, after composer install the dependencies will be downloaded
inside a folder named `vendor` in the root of your project. So you'll have to
create the following file in the root directory.

```php
<?php

/**
 * This file must to be called `index.php`
 */

try {
  include_once './vendor/thizer/cmthizer/core/CmThizer.php';
  
  $cms = new CmThizer();
  
  // Set your site files folder
  $cms->setSitePath(__DIR__.'/site/');
  
  // @todo: We need to adapt this method to receive an array of folders
  // $cms->appendPluginsPath(__DIR__.'/plugins/');
  
  // alias to loadPlugins method
  $cms->step1();
  
  // If you want to handle some loaded plugin...
  // $cms->getPlugin('MenusPages')->setActive(false);

  // alias to dispatchConfigs
  $cms->step2();
  
  // Dispatch 
  $cms->run();
  
} catch (Throwable $e) {
  
  /**
   * With Throwable instance PHP 7 can handle
   * whatever type of exception or error
   */
  render_error_page($e, SHOW_ERRORS);
}

```

Now you must to create the `site`, `assets` and `plugins` folders in the root of
your project too. So some other files...

```console
$ cd /path/to/your/awesome-project/

$ mkdir -p site/01.home/
$ mkdir -p site/02.blog/
$ mkdir -p assets/css/
$ mkdir -p assets/js/
$ mkdir -p assets/fonts/
$ mkdir -p assets/images/
$ mkdir -p plugins/
```




 
