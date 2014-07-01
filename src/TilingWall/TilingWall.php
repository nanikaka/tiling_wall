<?php
namespace TilingWall;

class TilingWall
{
	private $targets;
	private $target_num;
	private $node_num;

	private $best_aspect;
	private $best_nodes;
	private $nodes;

	private $is_sprinkle;
	private $precision;

	private $precision_config = array(
		0 => array(
			'loop' => 1,
			'eps'   => PHP_INT_MAX,
		),
		1 => array(
			'loop' => 10,
			'eps'   => 1e-2,
		),
		2 => array(
			'loop' => 50,
			'eps'   => 1e-4,
		),
		3 => array(
			'loop' => 100,
			'eps'   => 1e-6,
		),
	);

	public function setIsSprinkle($flag)
	{
		$this->is_sprinkle = $flag;
	}

	public function setPrecision($level)
	{
		$this->precision = $this->precision_config[max(0, min(count($this->precision_config), $level))];
	}

	public function __construct(array $targets)
	{
		$this->targets = $targets;
		$this->target_num = count($this->targets);
		$this->node_num = $this->target_num * 2 - 1;

		$this->is_sprinkle = false;
		$this->precision = $this->precision_config[0];

		$this->best_aspect = PHP_INT_MAX;
		$this->best_nodes = null;
	}

	private function init()
	{
		if ($this->is_sprinkle) {
			shuffle($this->targets);
		}
		$this->nodes = array();
		for ($i = 0; $i < $this->node_num; $i++) {
			$node = array(
				'node_id' => $i,
				'parent' => $i == 0 ? -1 : (int)(($i - 1) / 2),
				'left' => ($this->node_num <= $i * 2 + 1) ? -1 : ($i * 2 + 1),
				'right' => ($this->node_num <= $i * 2 + 2) ? -1 : ($i * 2 + 2),
				'type' => mt_rand(0, 1),
				'is_leaf' => $this->target_num <= $i,
				'x' => 0,
				'y' => 0,
				'width' => 0,
				'height' => 0,
				'aspect' => 0,
			);
			$this->nodes[] = $node;
		}

		foreach ($this->targets as $idx => $target) {
			$node_id = $this->target_num + $idx - 1;
			$this->nodes[$node_id]['width'] = (float)$target['width'];
			$this->nodes[$node_id]['height'] = (float)$target['height'];
			$this->nodes[$node_id]['aspect'] = $target['width'] / $target['height'];
			$this->nodes[$node_id]['name'] = $target['name'];
		}
	}

	private function calcAspect($node_id)
	{
		$node = &$this->nodes[$node_id];
		$left_aspect = $this->nodes[$node['left']]['aspect'];
		$right_aspect = $this->nodes[$node['right']]['aspect'];

		if ($node['type'] == 0) {
			$node['aspect'] = $left_aspect + $right_aspect;
		} else {
			$node['aspect'] = ($left_aspect * $right_aspect * 1.0) / ($left_aspect + $right_aspect);
		}
	}

	private function calcPosition($node_id)
	{
		$node = &$this->best_nodes[$node_id];

		$p = $this->best_nodes[$node['parent']];
		if ($p['type'] == 0) {
			$node['height'] = $p['height'];
			$node['width'] = $node['height'] * $node['aspect'];
		} else {
			$node['width'] = $p['width'];
			$node['height'] = $node['width'] / $node['aspect'];
		}

		if ($node['node_id'] == $p['left']) {
			$node['x'] = $p['x'];
			$node['y'] = $p['y'];
		} else {
			if ($p['type'] == 0) {
				$node['x'] = $p['x'] + $p['width'] - $node['width'];
				$node['y'] = $p['y'];
			} else {
				$node['y'] = $p['y'] + $p['height'] - $node['height'];
				$node['x'] = $p['x'];
			}
		}
	}

	public function arrangement(array $options) {
		$retry_loop = $this->precision['loop'];
		$total_width = $options['width'];
		$total_height = $options['height'];
		$aspect = $total_width / $total_height;

		do {
			$this->init();
			for ($i = $this->target_num - 2; 0 <= $i; $i--) {
				$this->calcAspect($i);
			}
			$aspect_diff = abs($aspect - $this->nodes[0]['aspect']);
			if ($this->best_nodes === null || $aspect_diff < abs($this->best_aspect - $aspect)) {
				$this->best_aspect = $this->nodes[0]['aspect'];
				$this->best_nodes = $this->nodes;
			}
		} while ($retry_loop-- != 0 && $this->precision['eps'] < abs($this->best_aspect - $aspect));

		$this->best_nodes[0]['width'] = $options['width'];
		$this->best_nodes[0]['height'] = $this->best_nodes[0]['width'] / $this->best_nodes[0]['aspect'];
		for ($i = 1; $i < $this->node_num; $i++) {
			$this->calcPosition($i);
		}

		return array_slice($this->best_nodes, $this->target_num - 1, $this->target_num, true);
	}

	public function arrangementHeightFree(array $options) {
		$this->init();

		for ($i = $this->target_num - 2; 0 <= $i; $i--) {
			$this->calcAspect($i);
		}
		$this->best_nodes = $this->nodes;

		$this->best_nodes[0]['width'] = $options['width'];
		$this->best_nodes[0]['height'] = $this->best_nodes[0]['width'] / $this->best_nodes[0]['aspect'];

		for ($i = 1; $i < $this->node_num; $i++) {
			$this->calcPosition($i);
		}

		return array_slice($this->best_nodes, $this->target_num - 1, $this->target_num, true);
	}
}
