<?php
/**
 * @author John Hargrove
 * 
 * Date: 1/3/11
 * Time: 4:38 PM
 */


class WPAM_PayPal_MassPayRequest
{
	const RECEIVERTYPE_EMAIL_ADDRESS = 'EmailAddress';
	const RECEIVERTYPE_USER_ID = 'UserId';

	private $receiverType;
	private $emailSubject;
	private $recipients = array();

	public function __construct($receiverType, $emailSubject)
	{
		if ( ! in_array( $receiverType, array(
			self::RECEIVERTYPE_USER_ID,
			self::RECEIVERTYPE_EMAIL_ADDRESS )))
			throw new InvalidArgumentException( __( 'receiverType must be one of the RECEIVERTYPE_* constants', 'affiliates-manager' ) );

		$this->receiverType = $receiverType;
		$this->emailSubject = $emailSubject;
	}

	public function addRecipient($recipient, $amount, $transactionId)
	{
		if (!preg_match('/^\$?[0-9]+(,[0-9]{3})*(\.[0-9]{2})?$/', $amount))
			throw new InvalidArgumentException( __( "'amount' must be a valid monetary value", 'affiliates-manager' ) );

		$this->recipients[] = array(
			'recipient' => $recipient,
			'amount' => $amount,
			'transactionId' => $transactionId
		);
	}

	public function getFields()
	{
		$fields = array();
		$fields['RECEIVERTYPE'] = $this->receiverType;
		$fields['EMAILSUBJECT'] = $this->emailSubject;

		$i = 0;
		foreach ($this->recipients as $recipient)
		{
			if ($this->receiverType == self::RECEIVERTYPE_USER_ID)
				$fields['L_RECEIVERID'.$i] = $recipient['recipient'];
			else if ($this->receiverType == self::RECEIVERTYPE_EMAIL_ADDRESS)
				$fields['L_EMAIL'.$i] = $recipient['recipient'];

			$fields['L_AMT'.$i] = $recipient['amount'];
			$fields['L_UNIQUEID'.$i] = $recipient['transactionId'];
			$i++;
		}
		
		return $fields;
	}
}
