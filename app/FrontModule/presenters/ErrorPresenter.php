<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */



/**
 * Error presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class Front_ErrorPresenter extends FrontPresenter
{

	/**
	 * @param  Exception
	 * @return void
	 */
	public function renderDefault($exception)
	{
	    if ($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = TRUE;
			$this->terminate();

		} elseif ($exception instanceof BadRequestException) {
			$this->template->message = $exception->getMessage();
			$this->setView('404'); // load template 404.phtml

		} else {
			$this->setView('500'); // load template 500.phtml
			Debug::log($exception, Debug::ERROR);
		}
	}

}
