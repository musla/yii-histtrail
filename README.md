HistTrail v0.1
==========

HistTrail is a behavior for [Yii framework](http://www.yiiframework.com) that allows to keep and set historic values
for ActiveRecord model field. 

One is able to set a value for a field with limited date range validity. Later it is possible to ask for a value valid in specified date.
This behavior is useful in situations when it is needed to get historic values as well as set future values of attribute. 

## Installation

Import migrations/tbl_hist_trail.sql into your database. 


```
'import'=>array(
    'application.extensions.histtrail.models.*',
)
```

To the ActiveRecord add a HistoryBehavior Behavior and set fields that should be traced. 

```
'HistoryBehavior' => array(
   'class' => 'application.extensions.histtrail.behaviors.HistoryBehavior',
	 'allowed'=>array('price'),
),
``` 

In the views:
```
		<?php $this->widget('ext.histtrail.widgets.HistTbInputVertical', array(
			'model'=>$model, 
			'form'=>$form,
			'type'=> 'text',
			'attribute'=>'price',
			'inputHtmlOptions'=>array('maxlength'=>'10','class'=>'span5'),
		)); ?>	
		
```
Please not that this widget depends on [YiiBooster](http://yiibooster.clevertech.biz). 
Also please note that the widget is 'hard coded' on top of TbInputVertical. You are welcome to change / rewrite this part. 

In the code:
Instead of 
```
$model->price
```
use 
```
$model->getHistoryValue('price', $date);
```


## Todo
Widget generalization.


## Bug tracker
If you find any bugs, please create an issue at [issue tracker for project Github repository](https://github.com/musla/yii-histtrail/issues).

## License
This work is licensed under a MIT license. Full text is included in the `LICENSE` file in the root of codebase.


[www.tomashnilica.com](http://www.tomashnilica.com)

Perfect web applications.
