<?php

class RM_User_Filter extends RM_Filter {
   
    public function __construct($request, $service) {
        
        $params = array(
        'rm_interval' => 'rm_interval',
        'rm_status' => 'rm_status',
        'rm_to_search'=>'rm_to_search'
        );
        $default_param_values = array('rm_interval' => 'all', 'rm_status' => 'all',
        'rm_to_search' => "", 'rm_reqpage' => '1');
        
        parent::__constuct($request,$service, $params, $default_param_values);
        $this->set_pagination();
    }

    public function get_records() {
       $this->records = $this->service->get_all_user_data($this->pagination->curr_page, $this->pagination->entries_per_page, $this->filters['rm_to_search'], $this->filters['rm_status'], $this->filters['rm_interval']);
       return $this->records; 
    }

     public function set_pagination(){
        $total_entries=null;
        $total_entries = count($this->service->get_all_user_data(1, 99999999, $this->filters['rm_to_search'], $this->filters['rm_status'], $this->filters['rm_interval']));
        
       if (isset($_POST['rm_interval']) || isset($_POST['rm_status']))
            $request->req['rm_reqpage'] = 1;
       
        $req_page = (isset($this->request->req['rm_reqpage']) && $this->request->req['rm_reqpage'] > 0) ? $this->request->req['rm_reqpage'] : 1;
        
        $this->pagination= new RM_Pagination($this->filters,$this->request->req['page'],$total_entries,$req_page);
    } 

}
