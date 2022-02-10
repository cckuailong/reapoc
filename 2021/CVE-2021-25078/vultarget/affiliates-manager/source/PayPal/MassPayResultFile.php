<?php
/**
 * @author John Hargrove
 * 
 * Date: 1/19/11
 * Time: 10:01 PM
 */

class WPAM_PayPal_MassPayResultFile
{
	private $dateSubmitted;     public function getDateSubmitted()   { return $this->dateSubmitted;   }
	private $dateProcessed;     public function getDateProcessed()   { return $this->dateProcessed;   }
	private $dateCompleted;     public function getDateCompleted()   { return $this->dateCompleted;   }
	private $subject;           public function getSubject()         { return $this->subject;         }

	private $paymentAmount;     public function getPaymentAmount()   { return $this->paymentAmount;   }
	private $paymentCount;      public function getPaymentCount()    { return $this->paymentCount;    }
	private $feeAmount;         public function getFeeAmount()       { return $this->feeAmount;       }
	private $totalAmount;       public function getTotalAmount()     { return $this->totalAmount;     }

	private $completedAmount;   public function getCompletedAmount() { return $this->completedAmount; }
	private $completedCount;    public function getCompletedCount()  { return $this->completedCount;  }
	private $unclaimedAmount;   public function getUnclaimedAmount() { return $this->unclaimedAmount; }
	private $unclaimedCount;    public function getUnclaimedCount()  { return $this->unclaimedCount;  }
	private $returnedAmount;    public function getReturnedAmount()  { return $this->returnedAmount;  }
	private $returnedCount;     public function getReturnedCount()   { return $this->returnedCount;   }
	private $deniedAmount;      public function getDeniedAmount()    { return $this->deniedAmount;    }
	private $deniedCount;       public function getDeniedCount()     { return $this->deniedCount;     }

	private $transactions;      public function getTransactions()    { return $this->transactions;    }


	private $csvHeader;

	public function __construct($filepath)
	{
		$this->transactions = array();
		$this->parseFile($filepath);
	}

	private function parseFile($file)
	{
		$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line)
		{
			$this->parseLine($line);
		}
	}

	private function parseLine($line)
	{
		if (preg_match('/^"?([^:]+): (.*)$/', $line, $matches))
		{
			$this->parseHeaderItem($matches[1], $matches[2]);
		}
		else if (preg_match('/^Transaction ID,(.*)$/', $line))
		{
			$this->csvHeader = explode(',', $line);
		}
		else if ($this->csvHeader !== NULL)
		{
			// assume all lines after the header was found are csv rows
			$transaction = array_map(
				array($this, 'removeQuotes'),
				array_combine($this->csvHeader, explode(',', $line))
			);
			preg_match('/(\d+\.\d+)/', $transaction['Amount'], $matches);
			$transaction['Amount'] = $matches[1];

			preg_match('/(\d+\.\d+)/', $transaction['Fee'], $matches);
			$transaction['Fee'] = $matches[1];

			$this->transactions[] = $transaction;
		}
	}

	private function removeQuotes($item)
	{
		return trim($item, '"');
	}

	private function parseHeaderItem($name, $value)
	{
		switch ($name)
		{
			case 'Transaction Type':
				if ($value !== 'Mass Payment')
					throw new Exception("Transaction type of '{$value}' is not supported!");
				break;
			case 'Date Submitted': $this->dateSubmitted = strtotime($value); break;
			case 'Date Processed': $this->dateProcessed = strtotime($value); break;
			case 'Date Completed': $this->dateCompleted = strtotime($value); break;
			case 'Subject': $this->subject = $value; break;
			case 'Payment Amount': $this->parseAmountAndCount($value, $this->paymentAmount, $this->paymentCount); break;
			case 'Fee Amount': $this->parseAmount($value, $this->feeAmount); break;
			case 'Total Amount': $this->parseAmount($value, $this->totalAmount); break;
			case 'Completed Amount': $this->parseAmountAndCount($value, $this->completedAmount, $this->completedCount); break;
			case 'Unclaimed Amount': $this->parseAmountAndCount($value, $this->unclaimedAmount, $this->unclaimedCount); break;
			case 'Returned Amount': $this->parseAmountAndCount($value, $this->returnedAmount, $this->returnedCount); break;
			case 'Denied Amount': $this->parseAmountAndCount($value, $this->deniedAmount, $this->deniedCount); break;
		}
	}

	private function parseAmountAndCount($value, &$amount, &$count)
	{
		if (preg_match('/(\d+\.\d+) (\w+) in (\d+) payments?/i', $value, $matches))
		{
			$amount = $matches[1];
			$count = $matches[3];
		}
		else
		{
			throw new Exception( sprintf( __('Failed to parse amounts from field: %s', 'affiliates-manager' ), $value ) );
		}
	}
	private function parseAmount($value, &$amount)
	{
		if (preg_match('/(\d+\.\d+) (\w+)/i', $value, $matches))
		{
			$amount = $matches[1];
		}
		else
		{
			throw new Exception( sprintf( __('Failed to parse amounts from field: %s', 'affiliates-manager' ), $value ) );
		}
	}
}
