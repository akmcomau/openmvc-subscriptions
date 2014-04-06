<?php

namespace modules\subscriptions\classes;

use core\classes\exceptions\RedirectException;
use core\classes\Hook;
use core\classes\Model;
use core\classes\Authentication;
use core\classes\models\Customer;
use core\classes\models\Administrator;

class Hooks extends Hook {

	public function init_authentication(Authentication $auth) {
		$model = new Model($this->config, $this->database);
	}
}