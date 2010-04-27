<?php
require_once(dirname(__FILE__).'/AnswerScheduleSuccessView.class.php');

/**
 * コミュニティのスケジュール決定
 *
 * @author  z-satosi
 * @version $Revision: 1.2 $
 */

class DecideScheduleSuccessView extends AnswerScheduleSuccessView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$this->setAttribute('is_decision_screen', TRUE);
		$this->setAttribute('mailentry_adjustment_id', 
				$request->getAttribute('mailentry_adjustment_id'));
		$this->setAttribute('current_module','Community');
		$this->setAttribute('current_action', 'DecideSchedule');
		return parent::execute();
	}
}
?>
