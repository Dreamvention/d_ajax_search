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
        $this->load->language($this->route);
        $data['results_for'] = $this->language->get('results_for');
        $data['more_results'] = $this->language->get('more_results');
        $data['search_phase']= $this->language->get('search_phase');
        $setting1 = $this->model_setting_setting->getSetting($this->id);
        if(empty($this->request->get['route']) || !empty($this->request->get['route']) && ($this->request->get['route'] != 'checkout/checkout')){
            $this->document->addScript('catalog/view/javascript/d_ajax_search/jquery.tinysort.min.js');
        }
        if (preg_match('/(iPhone|iPod|iPad|Android|Windows Phone)/', $this->request->server['HTTP_USER_AGENT'])) {
            $mobile = $data['mobile'] = 1;
             $this->document->addStyle('catalog/view/theme/default/stylesheet/' . $this->id . '_mobile.css');
        }
        else {
            $mobile = $data['mobile'] = 0;
             $this->document->addStyle('catalog/view/theme/default/stylesheet/' . $this->id . '.css');
        }
        if(isset($setting1['d_ajax_search_setting'])){
            $settings = $setting1['d_ajax_search_setting'];
            $data['setting']=$settings;
            if($setting1['d_ajax_search_status'] == 1){
                return $this->model_extension_d_opencart_patch_load->view('' . $this->route, $data);
            }
        }
    }

    public function view_common_header_after(&$route, &$data, &$output){
        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
        $html_dom->find('body', 0)->innertext .= $this->load->controller('extension/module/d_ajax_search');
        $output = (string)$html_dom;
    }

    public function write_to_base(){
        if(isset($this->request->post)){
            $this->model_extension_module_d_ajax_search->save_statistic($this->request->post);
            $this->response->setOutput(json_encode('ok'));
        }
        $this->response->setOutput(json_encode('error'));
    }

    public function getAutocomplite(){
        if(isset($this->request->get)){
            $keyword=$this->request->get['keyword'];
        }else{
            $keyword='';
        }

        $result=$this->model_extension_module_d_ajax_search->autocomplite($keyword);
        $this->response->setOutput(json_encode($result));
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