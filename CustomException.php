<?php

const GS_ERROR1 = 4000;
const GS_ERROR2 = 4010;

class CustomException extends Exception {



	public function __construct($message, $code, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);

	}

}