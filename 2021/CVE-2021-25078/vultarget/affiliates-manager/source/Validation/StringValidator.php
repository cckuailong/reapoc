<?php
/**
 * @author John Hargrove
 * 
 * Date: May 30, 2010
 * Time: 9:16:20 PM
 */

class WPAM_Validation_StringValidator implements WPAM_Validation_IValidator
{
	private $minLength;
	private $maxLength;

	public function __construct($minLength = NULL, $maxLength = NULL)
	{
		$this->minLength = $minLength;
		$this->maxLength = $maxLength;
	}

	public function getError()
	{
		if ($this->minLength > 1 && $this->maxLength)
		{
			return sprintf( __( 'must be between % and %s characters', 'affiliates-manager' ), $this->minLength, $this->maxLength );
		}
		else if ($this->minLength >= 1)
		{
			if ($this->minLength == 1)
				return __( 'is required', 'affiliates-manager' );
			else
				return sprintf( __( 'must be at least %s characters', 'affiliates-manager' ), $this->minLength );
		}
		else if ($this->maxLength)
		{
			return sprintf( __( 'must be no more than %s characters', 'affiliates-manager' ), $this->maxLength );
		}
	}

	public function isValid($value)
	{
		$l = strlen($value);

		return (($this->minLength === NULL || $l >= $this->minLength)
			&& ($this->maxLength === NULL || $l <= $this->maxLength));		
	}

}
