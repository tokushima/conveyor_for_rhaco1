<?php
Rhaco::import("model.WorkerBase");
/**
 * @author kazutaka tokushima
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterBase extends WorkerBase{
	/**
	 * check if variable is type of Rss20 object.
	 *
	 * @param unknown_type $variable
	 * @return boolean
	 * TODO: s/verfy/verify/
	 */
	function verfy($variable){
		/***
		 * assert(FilterBase::verfy(new Rss20()));
		 * eq(false,FilterBase::verfy(new Rss()));
		 */
		return (Variable::istype("Rss20",$variable));
	}
}
?>