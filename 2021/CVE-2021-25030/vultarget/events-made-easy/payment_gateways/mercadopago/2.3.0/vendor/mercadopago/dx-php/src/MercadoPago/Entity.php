<?php
namespace MercadoPago;
use MercadoPago\Annotation\Attribute;
use Exception;
/**
 * Class Entity
 *
 * @package MercadoPago
 */
abstract class Entity
{
    /**
     * @var
     */
    
    protected static $_custom_headers = array();
    protected static $_manager;
    /**
     * @Attribute(serialize = false)
     */
    protected $_last;
    protected $error;
    protected $_pagination_params;
    /**
     * @Attribute(serialize = false)
     */
    protected $_empty = false;
    /**
     * Entity constructor.
     *
     * @param array $params
     *
     * @throws \Exception
     */
    public function __construct($params = [])
    {
        if (empty(self::$_manager)) {
            throw new \Exception('Please initialize SDK first');
        }
        self::$_manager->setEntityMetaData($this);
        $this->_fillFromArray($this, $params);
    }

    /**
     */
    public function Error()
    {
        return $this->error;
    }
    /**
     * @param Manager $manager
     */
    public static function setManager(Manager $manager)
    {
        self::$_manager = $manager;
    }
    /**
     */
    public static function unSetManager()
    {
        self::$_manager = null;
    }
    /**
     * @return mixed
     */
    public static function get($id)
    {
      return self::read(array("id" => $id));
    }
    /**
     * @return mixed
     */
    public static function find_by_id($id)
    { 
      return self::read(array("id" => $id));
    }
    public static function setCustomHeader($key, $value)
    {
      self::$_custom_headers[$key] = $value;
    } 
    public static function getCustomHeader($key)
    {
      return self::$_custom_headers[$key];
    } 
    public static function setCustomHeadersFromArray($array){
      foreach ($array as $key => $value){ 
        self::setCustomHeader($key, $value);
      } 
    }
    public static function getCustomHeaders()
    {
      return self::$_custom_headers;
    }

    /**
     * @return mixed
     */
    public function not_found()
    { 
        return $this->_empty;
    }

    /**
     * @return mixed
     */
    public static function read($params = [], $options = [])
    { 
    
        $class = get_called_class();
        $entity = new $class();

        self::$_manager->setEntityUrl($entity, 'read', $params); 
        self::$_manager->cleanEntityDeltaQueryJsonData($entity);
        
        $response =  self::$_manager->execute($entity, 'get', $options);
        
        if ($response['code'] == "200" || $response['code'] == "201") {
            $entity->_fillFromArray($entity, $response['body']);
            $entity->_last = clone $entity;
            return $entity;
        } elseif (intval($response['code']) == 404) {
            return null;
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            throw new Exception ($response['body']['message']);
        } else {
            throw new Exception ("Internal API Error");
        }

    }

    /**
     * @return mixed
     */
    public static function all($options = [])
    {
        $params = [];
        $class = get_called_class();
        $entity = new $class();
        $entities =  array();

        self::$_manager->setEntityUrl($entity, 'list', $params);
        self::$_manager->cleanQueryParams($entity);
        $response = self::$_manager->execute($entity, 'get');
      
        if ($response['code'] == "200" || $response['code'] == "201") {
            $results = $response['body'];
            foreach ($results as $result) {
                $entity = new $class();
                $entity->_fillFromArray($entity, $result); 
                array_push($entities, $entity);
            }
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            throw new Exception ($response['error'] . " " . $response['message']);
        } else {
            throw new Exception ("Internal API Error");
        }
        return $entities; 
    }

    /**
     * @return mixed
     */
    public static function search($filters = [], $options = [])
    {
        $class = get_called_class();
        $searchResult = new SearchResultsArray();
        $searchResult->setEntityTypes($class);
        $entityToQuery = new $class();
        
        self::$_manager->setEntityUrl($entityToQuery, 'search');
        self::$_manager->cleanQueryParams($entityToQuery);
        self::$_manager->setQueryParams($entityToQuery, $filters);

        $response = self::$_manager->execute($entityToQuery, 'get');
        if ($response['code'] == "200" || $response['code'] == "201") {
            $results = $response['body']['results'];
            foreach ($results as $result) {
                $entity = new $class();
                $entity->_fillFromArray($entity, $result);
                $searchResult->append($entity);
            }
            $searchResult->setPaginateParams($response['body']['paging']);
            $searchResult->_filters = $filters;
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            $searchResult->process_error_body($response['body']);
            throw new Exception ($response['body']['message']);
        } else {
            throw new Exception ("Internal API Error");
        }
        return $searchResult;
    }
    /**
     * @codeCoverageIgnore
     * @return mixed
     */
    public function APCIteratorAll()
    {
        self::$_manager->setEntityUrl($this, 'list');
        return self::$_manager->execute($this, 'get');
    }

    /**
     * @return mixed
     */
    public function update($options = [])
    {   
        $params = [];
        self::$_manager->setEntityUrl($this, 'update', $params);
        self::$_manager->setEntityDeltaQueryJsonData($this);

        $response =  self::$_manager->execute($this, 'put');

        if ($response['code'] == "200" || $response['code'] == "201") {
            
            $this->_fillFromArray($this, $response['body']); 
            return true;
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            // A recuperable error 
            $this->process_error_body($response['body']); 
            return false;
        } else {
            throw new Exception ("Internal API Error");
        }
    }
    /**
     * @codeCoverageIgnore
     * @return mixed
     */
    public static function destroy()
    {
        //return self::$_manager->execute(get_called_class(), '');
    }

    /**
     * @return mixed
     */
    public function custom_action($method, $action)
    {
      self::$_manager->setEntityUrl($this, $action);
      self::$_manager->setEntityQueryJsonData($this);
      $response = self::$_manager->execute($this, $method);
      if ($response['code'] == "200" || $response['code'] == "201") {
          $this->_fillFromArray($this, $response['body']);
      }
      return $response;
    }

    /**
     * @return mixed
     */
    public function save($options = [])
    { 
        self::$_manager->setEntityUrl($this, 'create');
        self::$_manager->setEntityQueryJsonData($this);
        
        $response = self::$_manager->execute($this, 'post', $options);

        if ($response['code'] == "200" || $response['code'] == "201") {
            $this->_fillFromArray($this, $response['body']);
            $this->_last = clone $this;
            return true;
        } elseif (intval($response['code']) >= 300 && intval($response['code']) < 500) {
            // A recuperable error
            $this->process_error_body($response['body']);
            return false;
        } else {
            // Trigger an exception
            throw new Exception ("Internal API Error");
        }
    }

    function process_error_body($message){
        $recuperable_error = new RecuperableError(
            $message['message'],
            $message['error'],
            $message['status']
        );
        $recuperable_error->proccess_causes($message['cause']);
        $this->error = $recuperable_error;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->{$name};
    }

    

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->{$name});
    }
    /**
     * @param $name
     * @param $value
     *
     * @return mixed
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $this->_setValue($name, $value);
        return $this->{$name};
    }
    /**
     * @param null $attributes
     *
     * @return array
     */
    public function getAttributes() {
        return get_object_vars($this);
    }
     /**
     * @param null $attributes
     *
     * @return array
     */
    public function toArray($attributes = null)
    {
        $result = null;

        $excluded_attributes = self::$_manager->getExcludedAttributes($this);

        if (is_null($attributes)) {
            $result = get_object_vars($this);
        } else {
            $result = array_intersect_key(get_object_vars($this), $attributes);
        }        

        foreach ($excluded_attributes as $excluded_attribute) { 
            unset($result[$excluded_attribute]);
        }

        foreach ($result as $key => $value) { 
            if (!is_bool($value) && empty($value)) {
                unset($result[$key]);
            }
        }

        return $result;
    
    }
    /**
     * @param $property
     * @param $value
     *
     * @throws \Exception
     */
    protected function _setValue($property, $value, $validate = true)
    {
        if ($this->_propertyExists($property)) {
            if ($validate) {
                self::$_manager->validateAttribute($this, $property, ['maxLength','readOnly'], $value);
            }
            if ($this->_propertyTypeAllowed($property, $value)) {
                $this->{$property} = $value;
            } else {
                $this->{$property} = $this->tryFormat($value, $this->_getPropertyType($property), $property);
            }
        } else {
            if ($this->_getDynamicAttributeDenied()) {
                throw new \Exception('Dynamic attribute: ' . $property . ' not allowed for entity ' . get_class($this));
            }
            $this->{$property} = $value;
        }
    }
    /**
     * @param $property
     *
     * @return bool
     */
    protected function _propertyExists($property)
    {
        return array_key_exists($property, get_object_vars($this));
    }
    /**
     * @param $property
     * @param $type
     *
     * @return bool
     */
    protected function _propertyTypeAllowed($property, $type)
    {
        $definedType = $this->_getPropertyType($property);
        if (!$definedType) {
            return true;
        }
        if (is_object($type) && class_exists($definedType, false)) {
            return ($type instanceof $definedType);
        }
        return gettype($type) == $definedType;
    }
    /**
     * @param $property
     *
     * @return mixed
     */
    protected function _getPropertyType($property)
    {
        return self::$_manager->getPropertyType(get_called_class(), $property);
    }
    /**
     * @return mixed
     */
    protected function _getDynamicAttributeDenied()
    {
        return self::$_manager->getDynamicAttributeDenied(get_called_class());
    }
    /**
     * @param $value
     * @param $type
     * @param $property
     *
     * @return array|bool|float|int|string
     * @throws \Exception
     */
    protected function tryFormat($value, $type, $property)
    {
        try {
            switch ($type) {
                case 'float':
                    if (!is_numeric($value)) {
                        break;
                    }
                    return (float)$value;
                case 'int':
                    if (!is_numeric($value)) {
                        break;
                    }
                    return (int)$value;
                case 'string':
                    return (string)$value;
                case 'array':
                    return (array)$value;
                case 'date':
                    if (empty($value)) {
                        return $value;
                    };
                    if (is_string($value)) {
                        return date("Y-m-d\TH:i:s.000P", strtotime($value));
                    } else {
                        return $value->format('Y-m-d\TH:i:s.000P');
                    }
                    
            }
        } catch (\Exception $e) {
            throw new \Exception('Wrong type ' . gettype($value) . '. Cannot convert ' . $type . ' for property ' . $property);
        }
        throw new \Exception('Wrong type ' . gettype($value) . '. It should be ' . $type . ' for property ' . $property);
    }
    /**
     * Fill entity from data with nested object creation
     *
     * @param $entity
     * @param $data
     */
    protected function _fillFromArray($entity, $data)
    { 
      
      if ($data) {
        
        foreach ($data as $key => $value) {
            if (!is_null($value)){
                if (is_array($value)) {
                    $className = 'MercadoPago\\' . $this->_camelize($key);
                    if (class_exists($className, true)) {
                        $entity->_setValue($key, new $className, false);
                        $entity->_fillFromArray($this->{$key}, $value);
                    } else {
                        $entity->_setValue($key, json_decode(json_encode($value)), false);
                    }
                    continue;
                }
                $entity->_setValue($key, $value, false);
            }
        }
      }
    }
    /**
     * @param        $input
     * @param string $separator
     *
     * @return mixed
     */
    protected function _camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }


    public function delete($options = [])
    {
        $params = [];
        self::$_manager->setEntityUrl($this, 'delete', $params);

        $response =  self::$_manager->execute($this, 'delete');

        if ($response['code'] == "200" || $response['code'] == "201") {
            $this->_fillFromArray($this, $response['body']);
            return true;
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            if (!is_null($response['body'])){
                $this->process_error_body($response['body']);
            }
            return false;
        } else {
            throw new Exception ("Internal API Error");
        }
    }
}

