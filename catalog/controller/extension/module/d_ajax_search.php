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
        $data['no_results'] = $this->language->get('no_results');
        $data['more_results'] = $this->language->get('more_results');
        $data['search_phase']= $this->language->get('search_phase');
        $data['all_results'] = $this->language->get('all_results');
        
        $setting1 = $this->model_setting_setting->getSetting($this->id);
        if( !empty($setting1)&&$setting1['d_ajax_search_status']){
            if(empty($this->request->get['route']) || !empty($this->request->get['route']) && ($this->request->get['route'] != 'checkout/checkout')){
                $this->document->addScript('catalog/view/javascript/d_tinysort/tinysort.min.js');
                $this->document->addScript('catalog/view/javascript/d_tinysort/jquery.tinysort.min.js');
            }
            if (preg_match('/(iPhone|iPod|iPad|Android|Windows Phone)/', $this->request->server['HTTP_USER_AGENT'])) {
                $mobile = $data['mobile'] = 1;
                 $this->document->addStyle('catalog/view/theme/default/stylesheet/d_ajax_search/mobile.css');
            }
            else {
                $mobile = $data['mobile'] = 0;
                $this->document->addStyle('catalog/view/theme/default/stylesheet/d_ajax_search/d_ajax_search.css');
            }
            if(isset($setting1['d_ajax_search_setting'])){
                $settings = $setting1['d_ajax_search_setting'];
                $data['setting']=$settings;
                $data['setting']['class'] = str_replace('&gt;', " ", $data['setting']['class']);

                if($setting1['d_ajax_search_status'] == 1){
                    return $this->model_extension_d_opencart_patch_load->view('' . $this->route, $data);
                }
            }
        }
    }

    public function view_common_header_after(&$route, &$data, &$output){
        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
        $html_dom->find('body', 0)->innertext .= $this->load->controller('extension/module/d_ajax_search');
        $output = (string)$html_dom;
    }

    public function controller_common_header_before($route, &$data){
        $data['d_ajax_search'] = $this->load->controller('extension/module/d_ajax_search');
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