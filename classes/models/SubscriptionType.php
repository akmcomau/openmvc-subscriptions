<?php

namespace modules\subscriptions\classes\models;

use core\classes\Model;

class SubscriptionType extends Model {

	protected $table       = 'subscription_type';
	protected $primary_key = 'subscription_type_id';
	protected $columns     = [
		'subscription_type_id' => [
			'data_type'      => 'int',
			'auto_increment' => TRUE,
			'null_allowed'   => FALSE,
		],
		'site_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'subscription_type_active' => [
			'data_type'      => 'bool',
			'null_allowed'   => FALSE,
			'default_value'  => 'TRUE',
		],
		'subscription_type_name' => [
			'data_type'      => 'text',
			'data_length'    => 128,
			'null_allowed'   => FALSE,
		],
		'subscription_type_description' => [
			'data_type'      => 'text',
			'data_length'    => 256,
			'null_allowed'   => FALSE,
		],
		'subscription_type_price' => [
			'data_type'      => 'numeric',
			'data_length'    => [6, 4],
			'null_allowed'   => FALSE,
		],
		'subscription_type_period' => [
			'data_type'      => 'text',
			'data_length'    => 10,
			'null_allowed'   => FALSE,
		],
		'subscription_type_period_length' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'subscription_type_data' => [
			'data_type'      => 'text',
			'null_allowed'   => TRUE,
		],
	];

	protected $indexes = [
		'site_id',
		'subscription_type_active',
	];

	public function getPeriodString($language) {
		$string = $this->period_length.' ';
		if ($this->period == 'months') {
			$string .= ($this->period_length > 1) ? $language->get('months') : $language->get('month');
		}
		else {
			$string .= ($this->period_length > 1) ? $language->get('days') : $language->get('day');
		}
		return $string;
	}
}
