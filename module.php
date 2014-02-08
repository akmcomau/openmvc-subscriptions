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
	"hooks" => [
		"authentication" => [
			"init_authentication" => "classes\\Hooks",
		]
	],
	"default_config" => [
	]
];
