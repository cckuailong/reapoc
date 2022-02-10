<?php
namespace MercadoPago\Config;

/**
 * AbstractConfig Class Doc Comment
 *
 * @package MercadoPago\Config
 */
abstract class AbstractConfig
{
    /**
     * @var array|null
     */
    protected $data = null;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * AbstractConfig constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = array_merge($this->getDefaults(), $data);
    }

    /**
     * @return array
     */
    protected function getDefaults()
    {
        return [];
    }

    public function clean()
    {
        return $this->data = array(
            'base_url'      => 'https://api.mercadopago.com',
        );
    }


    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        
        if ($this->has($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return (array_key_exists($key, $this->data));
    }

    /**
     * @return array|null
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function configure ($data = [])
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

}