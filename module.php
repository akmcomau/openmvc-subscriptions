<?php
$_MODULE = [
	"name" => "Subscriptions",
	"description" => "Support for subscriptions",
	"namespace" => "\\modules\\subscriptions",
	"config_controller" => "administrator\\Subscriptions",
	"controllers" => [
		"Subscriptions",
		"customer\\Subscriptions",
		"administrator\\Subscriptions"
	],
	"default_config" => [
	]
];