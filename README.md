# flowchart-to-png

Setup
-----

 Add the library to your `composer.json` file in your project:

```javascript
{
  "require": {
      "renato127/flowchart-to-png": "0.*"
  }
}
```

Use [composer](http://getcomposer.org) to install the library:

```bash
$ php composer.phar install
```

Composer will install Math PHP inside your vendor folder. Then you can add the following to your
.php files to use the library with Autoloading.

```php
require_once(__DIR__ . '/vendor/autoload.php');
```

Usage
-----


```php

$flowChartImage = new FlowChartImage();
$flowChartImage->setContent($pendencia->fluxo->flowchart);
$flowChartImage->generate()->toPng($path);

```
