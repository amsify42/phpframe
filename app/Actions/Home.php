<?php

namespace App\Actions;

use PHPattern\Action;

class Home extends Action
{
	public function index()
	{
		return response()->json('Welcome to PHPFrame');
	}
}