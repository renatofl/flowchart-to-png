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

Composer will install flowchart-to-png inside your vendor folder. Then you can add the following to your
.php files to use the library with Autoloading.

```php
require_once(__DIR__ . '/vendor/autoload.php');
```

Usage
-----


```php
$json = '{
  "nodes": [
    {
      "id": "flowchartStart",
      "type": "start",
      "text": "Start",
      "left": "20px",
      "top": "180px",
      "countSource": null
    },
    {
      "id": "flowchartEnd",
      "type": "end",
      "text": "End",
      "left": "940px",
      "top": "180px",
      "countSource": null
    },
    {
      "id": "flowchartWindow1489779664638",
      "type": "action",
      "text": "Approve",
      "left": "680px",
      "top": "260px",
      "action": "Approve",
      "extraParams": "",
      "countSource": "1"
    },
    {
      "id": "flowchartWindow1489779672763",
      "type": "action",
      "text": "Reject",
      "left": "620px",
      "top": "40px",
      "action": "Reject",
      "extraParams": "",
      "countSource": "1"
    }
  ],
  "edges": [
    {
      "source": "flowchartStart",
      "target": "flowchartWindow1489779664638",
      "data": {
        "label": "",
        "positionSource": "RightMiddle",
        "positionTarget": "LeftMiddle"
      }
    },
    {
      "source": "flowchartStart",
      "target": "flowchartWindow1489779672763",
      "data": {
        "label": "",
        "positionSource": "RightMiddle",
        "positionTarget": "LeftMiddle"
      }
    },
    {
      "source": "flowchartWindow1489779672763",
      "target": "flowchartEnd",
      "data": {
        "label": "Success",
        "return": "success",
        "positionSource": "RightMiddle",
        "positionTarget": "LeftMiddle"
      }
    },
    {
      "source": "flowchartWindow1489779664638",
      "target": "flowchartEnd",
      "data": {
        "label": "Success",
        "return": "success",
        "positionSource": "RightMiddle",
        "positionTarget": "LeftMiddle"
      }
    }
  ]
}';


$flowChartImage = new FlowChartImage();
$flowChartImage->setContent($json);
$flowChartImage->generate()->toPng($path);

```

Change Node Color

------

```php

$image->setSelectedAction('flowchartWindow1489779664638');
$image->setSelectedColor([255, 0, 0]);

