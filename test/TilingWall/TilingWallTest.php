<?php
namespace TilingWall;

use TilingWall\Exception\AssertionFailedException;

class TilingWallTest extends \PHPUnit_Framework_TestCase
{
	private $sample_targets = array(
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

	/**
	 * @dataProvider setPrecisionProvider
	 */
	public function testSetPrecision($precision, $expected, $msg)
	{
		$tw = new TilingWall($this->sample_targets);
		$tw->setPrecision($precision);


		$refl = new \ReflectionProperty('TilingWall\TilingWall', 'precision');
		$refl->setAccessible(true);
		$this->assertSame($expected, $refl->getValue($tw), $msg);
	}

	public function setPrecisionProvider()
	{
		return array(
			array(
				0,
				array(
					'loop' => 1,
					'eps'   => PHP_INT_MAX,
				),
				'normal case',
			),
			array(
				-1,
				array(
					'loop' => 1,
					'eps'   => PHP_INT_MAX,
				),
				'below 0 is treated as 0',
			),
			array(
				4,
				array(
					'loop' => 100,
					'eps'   => 1e-6,
				),
				'above maximum is treated as maximum',
			),
		);
	}
}
