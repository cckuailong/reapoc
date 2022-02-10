<?php

/*
  PHP Interface to Webmoney
  Written by Vagharshak Tozalakyan <vagh@armdex.com>
  License: GNU Public License
*/


define('WM_GET', 0);
define('WM_POST', 1);
define('WM_LINK', 2);

define('WM_RES_OK', 0);
define('WM_RES_FAIL', 1);
define('WM_RES_NOPARAM', 2);

define('WM_ALL_SUCCESS', 0);
define('WM_ALL_FAIL', 1);
define('WM_SUCCESS_FAIL', 2);

define('WM_RF_ERR1', 'The required parameter payee_purse is missing or incorrect');
define('WM_RF_ERR2', 'The required parameter payment_amount is missing or incorrect');
define('WM_RF_ERR3', 'The optional parameter payment_no is incorrect');
define('WM_RF_ERR4', 'The optional parameter payment_desc is incorrect');
define('WM_RF_ERR5', 'The optional parameter sim_mode is incorrect');
define('WM_RF_ERR6', 'The optional parameter result_url is incorrect');
define('WM_RF_ERR7', 'The optional parameter success_url is incorrect');
define('WM_RF_ERR8', 'The optional parameter success_method is incorrect');
define('WM_RF_ERR9', 'The optional parameter fail_url is incorrect');
define('WM_RF_ERR10', 'The optional parameter fail_method is incorrect');
define('WM_RF_ERR11', 'The optional parameter payment_creditdays is incorrect');

define('WM_PRF_REALMODE', 0);
define('WM_PRF_TESTMODE', 1);


class WM_Request
{

  var $payee_purse = '';
  var $payment_amount = 0.0;
  var $payment_no = -1;
  var $payment_desc = '';
  var $sim_mode = -1;
  var $result_url = '';
  var $success_url = '';
  var $success_method = -1;
  var $fail_url = '';
  var $fail_method = -1;
  var $payment_creditdays = -1;
  var $extra_fields = array();

  var $action = 'https://merchant.wmtransfer.com/lmi/payment.asp';
  var $btn_label = 'Pay Webmoney';
  var $form_id = 'webmoney_form';
  var $btn_img_url = '';

  function SetForm($output = true)
  {

    $frm = '<form method="post" id="'.$this->form_id.'" name="'.$this->form_id.'" action="' . htmlentities($this->action) . '">' . "\n";

    $tmp = $this->payee_purse;
    if (!preg_match('/^[ZREUD][0-9]{12}$/', $tmp))
    {
      trigger_error(WM_RF_ERR1, E_USER_ERROR);
    }
    $frm .= '<input type="hidden" name="LMI_PAYEE_PURSE" value="' . $tmp . '" />' . "\n";

    $tmp = $this->payment_amount;
    if (!is_numeric($tmp) || $tmp <= 0.0)
    {
      trigger_error(WM_RF_ERR2, E_USER_ERROR);
    }
    $frm .= '<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="' . floatval($tmp) . '" />' . "\n";

    if ($this->payment_no != -1)
    {
      $tmp = $this->payment_no;
      if (!is_int($tmp) || $tmp < 0 || $tmp > 2147483647)
      {
        trigger_error(WM_RF_ERR3, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_PAYMENT_NO" value="' . $tmp . '" />' . "\n";
    }

    if (!empty($this->payment_desc))
    {
      $tmp = trim($this->payment_desc);
      if (strlen($tmp) > 255)
      {
        trigger_error(WM_RF_ERR4, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_PAYMENT_DESC" value="' . htmlentities($tmp) . '" />' . "\n";
    }

    if ($this->sim_mode != -1)
    {
      $tmp = $this->sim_mode;
      if (!is_int($tmp))
      {
        trigger_error(WM_RF_ERR5, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_SIM_MODE" value="' . $tmp . '" />' . "\n";
    }

    if (!empty($this->result_url))
    {
      $tmp = $this->result_url;
      if (substr($tmp, 0, 7) != 'http://' && substr($tmp, 0, 8) != 'https://' && substr($tmp, 0, 7) != 'mailto:')
      {
        trigger_error(WM_RF_ERR6, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_RESULT_URL" value="' . htmlentities($tmp) . '" />' . "\n";
    }

    if (!empty($this->success_url))
    {
      $tmp = $this->success_url;
      if (substr($tmp, 0, 7) != 'http://' && substr($tmp, 0, 8) != 'https://')
      {
        trigger_error(WM_RF_ERR7, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_SUCCESS_URL" value="' . htmlentities($tmp) . '" />' . "\n";
    }

    if ($this->success_method != -1)
    {
      $tmp = $this->success_method;
      if (!is_int($tmp) || ($tmp != 0 && $tmp != 1 && $tmp != 2))
      {
        trigger_error(WM_RF_ERR8, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_SUCCESS_METHOD" value="' . $tmp . '" />' . "\n";
    }

    if (!empty($this->fail_url))
    {
      $tmp = $this->fail_url;
      if (substr($tmp, 0, 7) != 'http://' && substr($tmp, 0, 8) != 'https://')
      {
        trigger_error(WM_RF_ERR9, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_FAIL_URL" value="' . htmlentities($tmp) . '" />' . "\n";
    }

    if ($this->fail_method != -1)
    {
      $tmp = $this->fail_method;
      if (!is_int($tmp) || ($tmp != 0 && $tmp != 1 && $tmp != 2))
      {
        trigger_error(WM_RF_ERR10, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_FAIL_METHOD" value="' . $tmp . '" />' . "\n";
    }

    if ($this->payment_creditdays != -1)
    {
      $tmp = $this->payment_creditdays;
      if (!is_int($tmp) || $tmp <= 0)
      {
        trigger_error(WM_RF_ERR11, E_USER_ERROR);
      }
      $frm .= '<input type="hidden" name="LMI_PAYMENT_CREDITDAYS" value="' . $tmp . '" />' . "\n";
    }

    foreach ($this->extra_fields as $name=>$value)
    {
      $frm .= '<input type="hidden" name="' . htmlentities($name);
      $frm .= '" value="' . htmlentities($value) . '" />' . "\n";
    }

    if (!empty($this->btn_img_url))
       $frm .= '<input type="image" id="wmbtn" src="' . htmlentities($this->btn_img_url) . '" alt="' . htmlentities($this->btn_label) . '" title="' . htmlentities($this->btn_label) . '" />' . "\n";
    else
       $frm .= '<input type="submit" id="wmbtn" value="' . htmlentities($this->btn_label) . '" />' . "\n";

    $frm .= '</form>' . "\n";

    if ($output)
    {
      echo $frm;
    }

    return $frm;
  }

}


class WM_Prerequest
{

  var $payee_purse = '';
  var $payment_amount = '';
  var $payment_no = '';
  var $mode = '';
  var $payer_wm = '';
  var $paymer_number = '';
  var $paymer_email = '';
  var $telepat_phonenumber = '';
  var $telepat_orderid = '';
  var $payment_creditdays = '';
  var $extra_fields = array();

  function GetForm()
  {
    if (!isset($_POST['LMI_PREREQUEST']) || $_POST['LMI_PREREQUEST'] != 1)
    {
      return WM_RES_NOPARAM;
    }
    $this->payee_purse = @$_POST['LMI_PAYEE_PURSE'];
    $this->payment_amount = @$_POST['LMI_PAYMENT_AMOUNT'];
    $this->payment_no = @$_POST['LMI_PAYMENT_NO'];
    $this->mode = @$_POST['LMI_MODE'];
    $this->payer_wm = @$_POST['LMI_PAYER_WM'];
    $this->paymer_number = @$_POST['LMI_PAYMER_NUMBER'];
    $this->paymer_email = @$_POST['LMI_PAYMER_EMAIL'];
    $this->telepat_phonenumber = @$_POST['LMI_TELEPAT_PHONENUMBER'];
    $this->telepat_orderid = @$_POST['LMI_TELEPAT_ORDERID'];
    $this->payment_creditdays = @$_POST['LMI_PAYMENT_CREDITDAYS'];
    foreach ($_POST as $field=>$value)
    {
      if (substr($field, 0, 4) != 'LMI_')
      {
        $this->extra_fields[$field] = $value;
      }
    }
    return WM_RES_OK;
  }

}


class WM_Notification
{

  var $payee_purse = '';
  var $payment_amount = '';
  var $payment_no = '';
  var $mode = '';
  var $sys_invs_no = '';
  var $sys_trans_no = '';
  var $payer_purse = '';
  var $payer_wm = '';
  var $paymer_number = '';
  var $paymer_email = '';
  var $telepat_phonenumber = '';
  var $telepat_orderid = '';
  var $payment_creditdays = '';
  var $hash = '';
  var $sys_trans_date = '';
  var $secret_key = '';
  var $extra_fields = array();

  function GetForm()
  {
    if (!isset($_POST['LMI_PAYMENT_NO']) ||
      (isset($_POST['LMI_PREREQUEST']) && $_POST['LMI_PREREQUEST'] == 1))
    {
      return WM_RES_NOPARAM;
    }
    $this->payee_purse = @$_POST['LMI_PAYEE_PURSE'];
    $this->payment_amount = @$_POST['LMI_PAYMENT_AMOUNT'];
    $this->payment_no = @$_POST['LMI_PAYMENT_NO'];
    $this->mode = @$_POST['LMI_MODE'];
    $this->sys_invs_no = @$_POST['LMI_SYS_INVS_NO'];
    $this->sys_trans_no = @$_POST['LMI_SYS_TRANS_NO'];
    $this->payer_purse = @$_POST['LMI_PAYER_PURSE'];
    $this->payer_wm = @$_POST['LMI_PAYER_WM'];
    $this->paymer_number = @$_POST['LMI_PAYMER_NUMBER'];
    $this->paymer_email = @$_POST['LMI_PAYMER_EMAIL'];
    $this->telepat_phonenumber = @$_POST['LMI_TELEPAT_PHONENUMBER'];
    $this->telepat_orderid = @$_POST['LMI_TELEPAT_ORDERID'];
    $this->payment_creditdays = @$_POST['LMI_PAYMENT_CREDITDAYS'];
    $this->hash = @$_POST['LMI_HASH'];
    $this->sys_trans_date = @$_POST['LMI_SYS_TRANS_DATE'];
    $this->secret_key = @$_POST['LMI_SECRET_KEY'];
    foreach ($_POST as $field=>$value)
    {
      if (substr($field, 0, 4) != 'LMI_')
      {
        $this->extra_fields[$field] = $value;
      }
    }
  }

  function CheckMD5($payee_purse, $payment_amount, $payment_no, $secret_key)
  {
    $key = $payee_purse . $payment_amount . $payment_no;
    $key .= $this->mode . $this->sys_invs_no . $this->sys_trans_no;
    $key .= $this->sys_trans_date . $secret_key . $this->payer_purse;
    $key .= $this->payer_wm;
    // we use strtoupper() because of the differences between PHP and ASP...
    if ($this->hash == strtoupper(md5($key)))
    {
      return WM_RES_OK;
    }
    return WM_RES_FAIL;
  }

}


class WM_Result
{

  var $payment_no = '';
  var $sys_invs_no = '';
  var $sys_trans_no = '';
  var $sys_trans_date = '';
  var $extra_fields = array();

  var $method = WM_POST;

  function GetForm()
  {
    $vars = $_POST;
    if ($this->method == WM_GET)
    {
      $vars = $_GET;
    }
    if (!isset($vars['LMI_PAYMENT_NO']))
    {
      return WM_RES_NOPARAM;
    }
    $this->payment_no = @$vars['LMI_PAYMENT_NO'];
    $this->sys_invs_no = @$vars['LMI_SYS_INVS_NO'];
    $this->sys_trans_no = @$vars['LMI_SYS_TRANS_NO'];
    $this->sys_trans_date = @$vars['LMI_SYS_TRANS_DATE'];
    foreach ($vars as $field=>$value)
    {
      if (substr($field, 0, 4) != 'LMI_')
      {
        $this->extra_fields[$field] = $value;
      }
    }
    return WM_RES_OK;
  }

}


?>
