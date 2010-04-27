<?php
// $Id: DiaryCalendarView_inline.class.php,v 1.1 2006/03/03 09:34:53 z-akitsu Exp $

class DiaryCalendarInputView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		// set
		$this->setAttribute('new_calendar_html', $request->getAttribute('new_calendar_html'));

		// テンプレート
		$this->setTemplate('DiaryCalendar.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("DiaryCalendar", $this->render());

		// set
		return parent::execute();
	}
}

?>
