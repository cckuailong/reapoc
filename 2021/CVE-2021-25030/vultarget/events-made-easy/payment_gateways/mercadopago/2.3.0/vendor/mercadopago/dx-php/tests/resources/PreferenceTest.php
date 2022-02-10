<?php 
/**
 * EntityTest Class Doc Comment
 *
 * @package MercadoPago
 */
class PreferenceTest extends \PHPUnit\Framework\TestCase
{

    private static $last_preference;

    public static function setUpBeforeClass()
    {
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = new Dotenv\Dotenv(__DIR__, '../../.env');
            $dotenv->load();
        }

        MercadoPago\SDK::setAccessToken(getenv('ACCESS_TOKEN'));

    }

    public function testCreatePrefence()
    {

        $preference = new MercadoPago\Preference();

        # Building an item
        $item = new MercadoPago\Item();
        $item->title = "item";
        $item->quantity = 1;
        $item->unit_price = 100; 

        $preference->items = array($item);
        $preference->expiration_date_to = new DateTime('tomorrow');
        $preference->save();

        self::$last_preference = $preference;
      
        $this->assertTrue($preference->sandbox_init_point != null);
    }

    public function testFindPreferenceById(){  
        $preference = MercadoPago\Preference::find_by_id(self::$last_preference->id);
        $this->assertEquals($preference->id, self::$last_preference->id);
    }
}
?>