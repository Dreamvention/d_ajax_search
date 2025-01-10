<?php

namespace Opencart\Catalog\Controller\Extension\LiveSearch\Module;

class LiveSearch extends \Opencart\System\Engine\Controller {

    private $id = 'live_search';
    private $route = 'extension/live_search/module/live_search';


    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $this->model_extension_live_search_module_live_search->loadDependencies();
    }

    public function index(){

        $data=array();
        $this->load->language($this->route);
        
        $setting1 = $this->model_setting_setting->getSetting($this->id);
        if(!empty($setting1)&&$setting1['live_search_status']){
            $this->config->get('config_language');
            $this->document->addScript('extension/live_search/catalog/view/javascript/autojs/autojs.min.js');
            $this->document->addStyle('extension/dv_dialogify/catalog/view/stylesheet/dv_dialogify.min.css');
            $this->document->addScript('extension/dv_dialogify/catalog/view/javascript/dv_dialogify.min.js');

            $rtl = $setting1['live_search_rtl'] ?? [];
            if (!empty($rtl[$this->config->get('config_language')])) {
                $this->document->addStyle('extension/ripecss/catalog/view/stylesheet/ripe.rtl.css');
                if (preg_match('/(iPhone|iPod|iPad|Android|Windows Phone)/', $this->request->server['HTTP_USER_AGENT'])) {
                    $mobile = $data['mobile'] = 1;
                    $this->document->addStyle('extension/live_search/catalog/view/stylesheet/d_ajax_search/mobile.rtl.css');
                }
                else {
                    $mobile = $data['mobile'] = 0;
                    $this->document->addStyle('extension/live_search/catalog/view/stylesheet/d_ajax_search/d_ajax_search.rtl.css');
                }
            } else {
                $this->document->addStyle('extension/ripecss/catalog/view/stylesheet/ripe.css');
                if (preg_match('/(iPhone|iPod|iPad|Android|Windows Phone)/', $this->request->server['HTTP_USER_AGENT'])) {
                    $mobile = $data['mobile'] = 1;
                    $this->document->addStyle('extension/live_search/catalog/view/stylesheet/d_ajax_search/mobile.css');
                }
                else {
                    $mobile = $data['mobile'] = 0;
                    $this->document->addStyle('extension/live_search/catalog/view/stylesheet/d_ajax_search/d_ajax_search.css');
                }
            }
            $settings = $setting1['live_search_setting'];
            $data['setting']=$settings; 
            
            $data['setting']['class'] = html_entity_decode(str_replace('&gt;', " ", $data['setting']['class']));

            if($setting1['live_search_status'] == 1){
                return $this->load->view($this->route, $data);
            }
        }
    }

    public function view_common_header_after(&$route, &$data, &$output){
        $html_dom = new \Opencart\System\Library\Extension\DvSimpleHtmlDom\DvSimpleHtmlDom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
        $html_dom->find('body', 0)->innertext .= $this->load->controller($this->route);
        $output = (string)$html_dom;
    }

    public function controller_common_header_before($route, &$data){
        $this->load->controller($this->route);
    }
    
    public function write_to_base(){
        if(isset($this->request->post)){
            $this->model_extension_live_search_module_live_search->save_statistic($this->request->post);
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

        $result=$this->model_extension_live_search_module_live_search->autocomplite($keyword);
        $this->response->setOutput(json_encode($result));
    }

    public function searchresults(){

        if(isset($this->request->get)){
            $keyword=$this->request->get['keyword'];
        }else{
            $keyword='';
        }

        $setting1 = $this->model_setting_setting->getSetting($this->id);
        $settings = $setting1['live_search_setting'];
        
        $params=array();
        foreach ($settings['extension'] as $key => $value) {
            if($value['enabled'] == 1){
                array_push($params, $key);
            }
        }
        if(!empty($params) && $setting1['live_search_status'] == 1){
            $result=$this->model_extension_live_search_module_live_search->search($keyword,$params);
            $this->response->setOutput(json_encode($result));
        }

    }
}