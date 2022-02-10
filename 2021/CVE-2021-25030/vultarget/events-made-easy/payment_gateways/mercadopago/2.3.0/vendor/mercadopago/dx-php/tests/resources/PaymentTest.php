<?php 

use PHPUnit\Framework\TestCase;

/**
 * EntityTest Class Doc Comment
 *
 * @package MercadoPago
 */
class PaymentTest extends TestCase
{

    public static function setUpBeforeClass()
    {
        MercadoPago\SDK::cleanCredentials();
        
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = new Dotenv\Dotenv(__DIR__, '../../.env');
            $dotenv->load();
        }
        
        MercadoPago\SDK::setAccessToken(getenv('ACCESS_TOKEN'));
        MercadoPago\SDK::setMultipleCredentials(
            array(
                "MLA" => "MLA_ACCESS_TOKEN"
            )
        );

    }

    public function testCreateApprovedPayment()
    {

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = 141;
        $payment->token = $this->SingleUseCardToken('approved');
        $payment->description = "Ergonomic Silk Shirt";
        $payment->installments = 1;
        $payment->payment_method_id = "visa";
        $payment->payer = array(
            "email" => getenv('USER_EMAIL')
        );
        $payment->external_reference = "reftest";
        $payment->save();

        $this->assertEquals($payment->status, 'approved');
        
 
        return $payment;

    }

    /**
     * @depends testCreateApprovedPayment
     */
    public function testRefundPayment(MercadoPago\Payment $payment_created_previously) {

        $id = $payment_created_previously->id;

        $refund = new MercadoPago\Refund();
        $refund->payment_id = $id;
        $refund->save();

        sleep(10);

        $payment = MercadoPago\Payment::find_by_id($id);

        $this->assertEquals("refunded", $payment->status);

    }


    public function testCreateAnInvalidPayment()
    {

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = -200;

        $payment_status = $payment->save();

        $this->assertFalse($payment_status);
        $this->assertEquals($payment->error->causes[0]->description, "transaction_amount must be positive"); 

    }

    public function testSearchWithInvalidQueryFilters()
    {

        $filters = array(
            "incorrect_param" => "000"
        );

        try {
            $payments = MercadoPago\Payment::search($filters);  
        } catch(Exception $e) {
            $this->assertEquals($e->getMessage(), "the attribute incorrect_param is not a possible param");
        }

    }

    public function testCreatePendingPayment()
    {

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = 141;
        $payment->token = $this->SingleUseCardToken('in_process');
        $payment->description = "Ergonomic Silk Shirt";
        $payment->installments = 1;
        $payment->payment_method_id = "visa";
        $payment->payer = array(
            "email" => getenv('USER_EMAIL')
        );
        $payment->external_reference = "reftest";
        $payment->save();

        $this->assertEquals($payment->status, 'in_process'); 
 
        return $payment;

    }

    /**
     * @depends testCreatePendingPayment
     */
    public function testFindPaymentById(MercadoPago\Payment $payment_created_previously) {
        $payment = MercadoPago\Payment::find_by_id($payment_created_previously->id); 
        $this->assertEquals($payment->id, $payment_created_previously->id);
    }

    /**
     * @depends testCreatePendingPayment
     */
    public function testFindPaymentByNonExistentId(MercadoPago\Payment $payment_created_previously) {
        $payment = MercadoPago\Payment::find_by_id("123456"); 
        $this->assertEquals($payment, null);
    }

    /**
     * @depends testCreatePendingPayment
     */
    public function testPaymentsSearch(MercadoPago\Payment $payment_created_previously) {
 
        $filters = array(
            "external_reference" => $payment_created_previously->external_reference
        );

        $payments = MercadoPago\Payment::search($filters);
        $payments = $payments->getArrayCopy();
        $payment = end($payments);
 
        $this->assertEquals($payment->external_reference, $payment_created_previously->external_reference);

    }
    
    /**
     * @depends testCreatePendingPayment 
     */
    public function testCancelPayment(MercadoPago\Payment $payment_created_previously) {
        $payment_created_previously->status = "cancelled";
        $payment_created_previously->update();

        sleep(10);
        
        $payment = MercadoPago\Payment::find_by_id($payment_created_previously->id);
        $this->assertEquals("cancelled", $payment->status);
        
    }

    public function testPaymentWithCustomAccessToken() {
        $payment = new MercadoPago\Payment();

        $options = array(
            "custom_access_token" => "MLA"
        );
        
        $payment_status = $payment->save($options);

        $this->assertFalse($payment_status); // Marlformed access token error 
        
        $payment_status = $payment->save();

        $this->assertFalse($payment_status);
       

    }


    private function SingleUseCardToken($status){

        $cards_name_for_status = array(
            "approved" => "APRO",
            "in_process" => "CONT",
            "call_for_auth" => "CALL",
            "not_founds" => "FUND",
            "expirated" => "EXPI",
            "form_error" => "FORM",
            "general_error" => "OTHE",
        );

        $i_current_month = intval(date('m'));
        $i_current_year = intval(date('Y'));
        
        $security_code = rand(111, 999);
        $expiration_month = rand($i_current_month, 12);
        $expiration_year = rand($i_current_year + 2, 2999);
        $dni = rand(11111111, 99999999);

        $payload = array(
            "json_data" => array(
                "card_number" => "4508336715544174",
                "security_code" => (string)$security_code,
                "expiration_month" => str_pad($expiration_month, 2, '0', STR_PAD_LEFT),
                "expiration_year" => str_pad($expiration_year, 4, '0', STR_PAD_LEFT),
                "cardholder" => array(
                    "name" => $cards_name_for_status[$status],
                    "identification" => array(
                        "type" => "DNI",
                        "number" => (string)$dni
                    )
                )
            )
        );

        $response = MercadoPago\SDK::post('/v1/card_tokens', $payload);

        return $response['body']['id'];

    }


}

?>