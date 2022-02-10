<?php



use PHPUnit\Framework\TestCase;

/**
 * EntityTest Class Doc Comment
 *
 * @package MercadoPago
 */
class ConfigTest extends TestCase                                                                                       
{

    public static function setUpBeforeClass()
    {

        MercadoPago\SDK::cleanCredentials();

        if (file_exists(__DIR__ . '/../.env')) {
            $dotenv = new Dotenv\Dotenv(__DIR__, '/../.env');
            $dotenv->load();
        }

        MercadoPago\SDK::setClientId(getenv('CLIENT_ID'));
        MercadoPago\SDK::setClientSecret(getenv('CLIENT_SECRET')); 
    }

    /**
     * @covers                   MercadoPago\SDK
     */
    public function testSettings()
    {
        $this->assertEquals(getenv('CLIENT_ID'), MercadoPago\SDK::getClientId());
        $this->assertEquals(getenv('CLIENT_SECRET'), MercadoPago\SDK::getClientSecret());

    }
 
    /**
     * @covers                   MercadoPago\SDK
     */
    public function testDoGetToken()
    { 
        $this->assertNotNull(MercadoPago\SDK::getAccessToken());
    }

    public function testSetMultipleAT(){
        MercadoPago\SDK::setMultipleCredentials(
            array(
                "mla" => "MLA_AT",
                "mlb" => "MLB_AT"
            )
        );
        $this->assertNotNull(MercadoPago\SDK::config()->getData()['mla']);
        $this->assertNotNull(MercadoPago\SDK::config()->getData()['mlb']);
    }

}
