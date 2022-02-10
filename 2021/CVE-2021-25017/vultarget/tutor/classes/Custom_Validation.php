<?php
namespace TUTOR;

trait Custom_Validation
{

	/*
	*check whether order is asc or desc
	*/
	public function validate_order($order)
	{

		if($order === 'ASC' OR $order ==='DESC' OR $order ==='asc' OR $order ==='desc')
		{
			return true;
		}
		return false;

	}
}
?>