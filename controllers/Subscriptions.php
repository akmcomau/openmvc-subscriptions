<?php

namespace modules\subscriptions\controllers;

use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;

class Subscriptions extends Controller {

	public function index() {
		$this->language->loadLanguageFile('subscriptions.php', 'modules'.DS.'subscriptions');

		$model = new Model($this->config, $this->database);
		$type = $model->getModel('\modules\subscriptions\classes\models\SubscriptionType');
		$types = $type->getMulti([], ['price' => 'asc']);

		$data = [
			'types' => $types
		];

		$template = $this->getTemplate('pages/subscriptions.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

}