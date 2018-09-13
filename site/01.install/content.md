# {{ title }}{.text-success}
<hr>

This project is build to be really easy to install and mainly to be updated with
content. We decided to not include a database gateway because of this, the simple
structure we want to get here.

There's two ways to install this project. The first is download as it is and simple
modify this documents, as an example. The second and **recommanded** is use it
as a lib with:

`composer require thizer/cmthizer`.

As you probally know, it will download this package in a `vendor` folder in the
root of your project. So you'll have to create the following file
`DOCUMENT_ROOT/index.php`:

```php
<?php
try {
  include_once './vendor/thizer/cmthizer/core/CmThizer.php';
  
  $cms = new CmThizer();
  $cms->setSitePath(__DIR__.'/site/');
  $cms->step1(); // alias to loadPlugins method
  
//  $cms->getPlugin('MenusPages')->setActive(false);

  $cms->step2(); // alias to dispatchConfigs
  $cms->run();
  
} catch (Throwable $e) {
  
  /**
   * With Throwable instance PHP 7 can handle
   * whatever type of exception or error
   */
  render_error_page($e, SHOW_ERRORS);
}

```
