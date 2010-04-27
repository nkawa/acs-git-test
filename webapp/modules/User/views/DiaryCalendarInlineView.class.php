<?php
// $Id: DiaryCalendarView_inline.class.php,v 1.1 2006/03/03 09:34:53 z-akitsu Exp $

class DiaryCalendarInlineView extends InlineBaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DiaryCalendar.tpl.php');

		// set
		$this->setAttribute('new_calendar_html', $request->getAttribute('new_calendar_html'));
		return parent::execute();
	}
}

?>
