<?php

namespace modules\subscriptions;

use ErrorException;
use core\classes\Config;
use core\classes\Database;
use core\classes\Language;
use core\classes\Model;
use core\classes\Menu;

class Installer {
	protected $config;
	protected $database;

	public function __construct(Config $config, Database $database) {
		$this->config = $config;
		$this->database = $database;
	}

	public function install() {
		$model = new Model($this->config, $this->database);

		$table = $model->getModel('\\modules\\subscriptions\\classes\\models\\SubscriptionType');
		$table->createTable();
		$table->createIndexes();
		$table->createForeignKeys();

		$table = $model->getModel('\\modules\\subscriptions\\classes\\models\\Subscription');
		$table->createTable();
		$table->createIndexes();
		$table->createForeignKeys();

		$table = $model->getModel('\\modules\\subscriptions\\classes\\models\\CheckoutSubscription');
		$table->createTable();
		$table->createIndexes();
		$table->createForeignKeys();
	}

	public function uninstall() {
		$model = new Model($this->config, $this->database);

		$table = $model->getModel('\\modules\\subscriptions\\classes\\models\\CheckoutSubscription');
		$table->dropTable();

		$table = $model->getModel('\\modules\\subscriptions\\classes\\models\\Subscription');
		$table->dropTable();
		$table = $model->getModel('\\modules\\subscriptions\\classes\\models\\SubscriptionType');
		$table->dropTable();
	}

	public function enable() {
		$language = new Language($this->config);
		$language->loadLanguageFile('administrator/subscriptions.php', DS.'modules'.DS.'subscriptions');

		$layout_strings = $language->getFile('administrator/layout.php');
		$layout_strings['subscriptions_module_subscriptions'] = $language->get('subscriptions');
		$language->updateFile('administrator/layout.php', $layout_strings);

		$main_menu = new Menu($this->config, $language);
		$main_menu->loadMenu('menu_admin_main.php');
		$main_menu->insert_menu(['checkout', 'checkout_orders'], 'checkout_subscriptions', [
			'controller' => 'administrator/Subscriptions',
			'method' => 'index',
			'text_tag' => 'subscriptions_module_subscriptions',
			'children' => [
				'checkout_subscriptions_list' => [
					'controller' => 'administrator/Subscriptions',
					'method' => 'index',
				],
				'checkout_subscriptions_add' => [
					'controller' => 'administrator/Subscriptions',
					'method' => 'addSubscription',
				],
				'checkout_subscriptions_types' => [
					'controller' => 'administrator/Subscriptions',
					'method' => 'types',
				],
				'checkout_subscriptions_add_type' => [
					'controller' => 'administrator/Subscriptions',
					'method' => 'addType',
				],
				'checkout_subscriptions_report' => [
					'controller' => 'administrator/Subscriptions',
					'method' => 'report',
				],
			],
		]);
		$main_menu->update();

		$user_menu = new Menu($this->config, $language);
		$user_menu->loadMenu('menu_public_user.php');
		$user_menu->insert_menu(['account', 'account_orders'], 'account_subscriptions', [
			'controller' => 'customer/Subscriptions',
			'method' => 'index',
		]);
		$user_menu->update();

		$config = $this->config->getSiteConfig();
		$config['sites'][$this->config->getSiteDomain()]['checkout']['item_types']['subscription'] = [
			'checkout' => '\modules\subscriptions\classes\models\CheckoutSubscription',
			'item' => '\modules\subscriptions\classes\models\SubscriptionType',
		];
		$this->config->setSiteConfig($config);
	}

	public function disable() {
		$language = new Language($this->config);
		$language->loadLanguageFile('administrator/subscriptions.php', DS.'modules'.DS.'subscriptions');

		$layout_strings = $language->getFile('administrator/layout.php');
		unset($layout_strings['subscriptions_module_subscriptions']);
		$language->updateFile('administrator/layout.php', $layout_strings);

		// Remove some menu items to the admin menu
		$main_menu = new Menu($this->config, $language);
		$main_menu->loadMenu('menu_admin_main.php');
		$menu = $main_menu->getMenuData();
		unset($menu['checkout']['children']['checkout_subscriptions']);
		$main_menu->setMenuData($menu);
		$main_menu->update();

		$user_menu = new Menu($this->config, $language);
		$user_menu->loadMenu('menu_public_user.php');
		$menu = $user_menu->getMenuData();
		unset($menu['account']['children']['account_subscriptions']);
		$user_menu->setMenuData($menu);
		$user_menu->update();

		$config = $this->config->getSiteConfig();
		unset($config['sites'][$this->config->getSiteDomain()]['checkout']['item_types']['subscription']);
		$this->config->setSiteConfig($config);
	}
}