tiling_wall
===========

READMEは現在編集中です

# USAGE

## Example

```php
	$targets = array(
		array(
			'name' => 'http://hoge.com/image/1.jpg',
			'width' => 128,
			'height' => 128,
		),
		array(
			'name' => 'http://hoge.com/image/2.jpg',
			'width' => 123,
			'height' => 456,
		),
	);
	$params = array(
		'width' => 1200,
		'height' => 800,
		'precision_level' => 2,
		'sprinkle' => true,
	);

	$result = TilingWallClient::getArrangementResult($targets, $params);
```

# References

1. Zhipeng Wu and Kiyoharu Aizawa: "Building Friend Wall for Local Photo Repository by Using Social Attribute Annotation" JOURNAL OF MULTIMEDIA, VOL. 9, NO. 1, JANUARY 2014

