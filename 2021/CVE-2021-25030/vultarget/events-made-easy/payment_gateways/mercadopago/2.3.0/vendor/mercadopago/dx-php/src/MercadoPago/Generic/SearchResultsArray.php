<?php
    namespace MercadoPago;

    use ArrayObject;

    class SearchResultsArray extends ArrayObject {

        public $_filters;
        public $limit;
        public $total;
        public $offset;
        public $errors;
        public $_class;
        

        public function setEntityTypes($class){
            $this->_class = $class;
        }

        public function setPaginateParams($params){ 
            $this->limit  = $params["limit"];
            $this->total  = $params["total"];
            $this->offset = $params["offset"]; 
        }

        public function next() {
            
            $new_offset = $this->limit + $this->offset;
            echo "\n new offset" . $new_offset ;

            $this->_filters['offset'] = $new_offset; 
            
            $result = $this->_class::search($this->_filters);



            echo "\nlimit" . $result->limit ;
            echo "\nresult offset" . $result->offset ;
 
            $this->limit = $result->limit;
            $this->offset = $result->offset;
            $this->total = $result->total;

            $this->exchangeArray($result->getArrayCopy()); 

        }

        public function process_error_body($message){ 

            $recuperable_error = new RecuperableError(
                $message['message'],
                $message['error'],
                $message['status']
            );
    
            foreach ($message['cause'] as $causes) { 
                if(is_array($causes)) {
                    foreach ($causes as $cause) {
                        $recuperable_error->add_cause($cause['code'], $cause['description']);
                    }
                } else {
                    $recuperable_error->add_cause($cause['code'], $cause['description']);
                }
            }
          
            $this->errors = $recuperable_error;
        }

    }

?>