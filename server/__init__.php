<?php
	include_once("../__settings__.php");
	Rhaco::import("lang.Variable");
	if(!Variable::bool(Rhaco::constant("IS_SERVER"))) Rhaco::end();
?>