<?php
class ITC_Disable_Update_Notifications_Deactivator extends ITC_Disable_Update_Notifications_BaseController {

	public function __construct() {
		parent::__construct();// call parent constructor
	}

	public function deactivate() {
		$this->unregister_Module($this->get_plugin_name());
		$this->remove_transient();
	}

}
