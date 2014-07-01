<?php

include_once __DIR__ . '/TilingWall/TilingWall.php';

class TilingWallClient
{
	public static function getArrangementResult(array $targets, array $options)
	{
		$tiling_wall = new TilingWall($targets);

		if (isset($options['sprinkle'])) {
			$tiling_wall->setIsSprinkle($options['sprinkle']);
		}

		if (isset($options['height'])) {
			if (isset($options['precision_level'])) {
				$tiling_wall->setPrecision($options['precision_level']);
			} else {
				$tiling_wall->setPrecision(1);
			}
			$result = $tiling_wall->arrangement($options);
		} else {
			$result = $tiling_wall->arrangementHeightFree($options);
		}

		return $result;
	}
}
