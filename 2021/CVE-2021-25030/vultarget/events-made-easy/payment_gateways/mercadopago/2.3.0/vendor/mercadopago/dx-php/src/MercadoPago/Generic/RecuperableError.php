<?php
 
namespace MercadoPago;

class RecuperableError {

    public $message = "";
    public $status = "";
    public $error = "";

    public $causes = [];

    function __construct($message, $error, $status) {
        $this->message = $message;
        $this->status = $status;
        $this->error = $error;
    }

    public function add_cause($code, $description) {
        $error_cause = new ErrorCause();
        $error_cause->code = $code;
        $error_cause->description = $description;
        array_push($this->causes, $error_cause);
    }

    public function proccess_causes($causes){
        if(isset($causes['code']) && isset($causes['description'])){
            $this->add_cause($causes['code'], $causes['description']);
        }else{
            foreach ($causes as $cause){
                if(is_array($cause) && (!isset($cause['code']) && !isset($cause['description']))){
                    $this->proccess_causes($cause);
                }else{
                    $this->add_cause($cause['code'], $cause['description']);
                }
            }
        }
    }

    public function __toString()
    {
        return $this->error . ": " . $this->message;
    }

}

?>