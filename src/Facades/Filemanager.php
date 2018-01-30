<?php

namespace Divart\Filemanager\Facades;

use Illuminate\Support\Facades\Facade;

Class Filemanager extends Facade{

	protected static function getFacadeAccessor() {
		return 'filemanager';
	}
}