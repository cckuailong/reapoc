<?php
abstract class OptionElement extends Element {
	public $options;

	public function __construct($label, $name, array $options, array $properties = null, array $advance_opts = null) {
		$this->options = $options;
		if(!empty($this->options) && array_values($this->options) === $this->options)
			$this->options = array_combine($this->options, $this->options);
		
		parent::__construct($label, $name, $properties, $advance_opts);
	}

	public function getOptionValue($value) {
        $position = strpos($value, ":pfbc");
        if($position !== false) {
            if($position == 0)
                $value = "";
            else
                $value = substr($value, 0, $position);
        }
        return $value;
    }
}
