<?php
namespace IPTools;

/**
 * @author Safarov Alisher <alisher.safarov@outlook.com>
 * @link https://github.com/S1lentium/IPTools
 */
trait PropertyTrait
{
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if(method_exists($this, $name)) {
			return $this->$name();
		}

		foreach (array('get', 'to') as $prefix) {
            $method = $prefix . ucfirst($name);
            if(method_exists($this, $method)) {
                return $this->$method();
            }
        }

		trigger_error('Undefined property');
		return null;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$method = 'set'. ucfirst($name);
		if (!method_exists($this, $method)) {
			trigger_error('Undefined property');
			return;
		}
		$this->$method($value);
	}

}
