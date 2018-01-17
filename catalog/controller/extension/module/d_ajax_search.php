<?php
class ControllerExtensionModuleDAjaxSearch extends Controller {

    private $id = 'd_ajax_search';
    private $route = 'extension/module/d_ajax_search';


    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/load');
    }

    public function index(){
        $data=array();
        $this->document->addStyle('catalog/view/theme/default/stylesheet/' . $this->id . '.css');
        $setting1 = $this->model_setting_setting->getSetting($this->id);
        if(isset($setting1['d_ajax_search_setting'])){
            $settings = $setting1['d_ajax_search_setting'];
            $data['setting']=$settings;
            if($setting1['d_ajax_search_status'] == 1){
                return $this->model_extension_d_opencart_patch_load->view('' . $this->route, $data);
            }
        }
    }

    public function searchresults(){

        if(isset($this->request->get)){
            $keyword=$this->request->get['keyword'];
        }else{
            $keyword='';
        }

        $setting1 = $this->model_setting_setting->getSetting($this->id);
        $settings = $setting1['d_ajax_search_setting'];
        $params=array();
        foreach ($settings['extension'] as $key => $value) {
            if($value['enabled'] == 1){
                array_push($params, $key);
            }
        }
        if(!empty($params) && $setting1['d_ajax_search_status'] == 1){
            $result=$this->model_extension_module_d_ajax_search->search($keyword,$params);
            $this->response->setOutput(json_encode($result));
        }

    }
}