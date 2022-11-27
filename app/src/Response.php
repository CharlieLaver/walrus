<?php

class Response
{
	protected function return($data, $success = true)
	{
		echo json_encode([
			'success' => $success,
			'data' => $data,
		]);
		die();
	}
}