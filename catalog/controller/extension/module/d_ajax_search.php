<?php
class ControllerExtensionModuleDAjaxSearch extends Controller {

    private $id = 'd_ajax_search';
    private $route = 'extension/module/d_ajax_search';

    protected $scripts = array();

    protected $styles = array();


    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/load');
    }

    protected function renderLiveAjaxSearch(){

        $data=array();
        $this->load->language($this->route);
        $data['results_for'] = $this->language->get('results_for');
        $data['no_results'] = $this->language->get('no_results');
        $data['more_results'] = $this->language->get('more_results');
        $data['search_phase']= $this->language->get('search_phase');
        $data['all_results'] = $this->language->get('all_results');
        
        $setting1 = $this->model_setting_setting->getSetting($this->id);
        if(!empty($setting1)){
            $this->addScript('catalog/view/javascript/cash/cash.min.js');
            $this->addScript('catalog/view/javascript/axios/axios.min.js');
            $this->addScript('catalog/view/theme/default/javascript/d_ajax_search/autojs/autojs.min.js');
            $this->addStyle('catalog/view/javascript/d_dialogify/dv_dialogify.min.css');
            $this->addScript('catalog/view/javascript/d_dialogify/dv_dialogify.min.js');
            $rtl = $this->config->get('d_ajax_search_rtl');
            if (!empty($rtl[$this->session->data['language']])) {
                $this->addStyle('catalog/view/javascript/ripecss/ripe.rtl.css');
                if (preg_match('/(iPhone|iPod|iPad|Android|Windows Phone)/', $this->request->server['HTTP_USER_AGENT'])) {
                    $mobile = $data['mobile'] = 1;
                    $this->addStyle('catalog/view/theme/default/stylesheet/d_ajax_search/mobile.rtl.css');
                } else {
                    $mobile = $data['mobile'] = 0;
                    $this->addStyle('catalog/view/theme/default/stylesheet/d_ajax_search/d_ajax_search.rtl.css');
                }
            } else {
                $this->addStyle('catalog/view/javascript/ripecss/ripe.css');
                if (preg_match('/(iPhone|iPod|iPad|Android|Windows Phone)/', $this->request->server['HTTP_USER_AGENT'])) {
                    $mobile = $data['mobile'] = 1;
                    $this->addStyle('catalog/view/theme/default/stylesheet/d_ajax_search/mobile.css');
                } else {
                    $mobile = $data['mobile'] = 0;
                    $this->addStyle('catalog/view/theme/default/stylesheet/d_ajax_search/d_ajax_search.css');
                }
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
        $html_dom->find('body', 0)->innertext .= $this->renderLiveAjaxSearch();
        $output = $this->parseHeader((string)$html_dom);

    }
    
    public function write_to_base(){
        $rawData = file_get_contents('php://input');
        $post = json_decode($rawData, true);
        if(!$post){
            $post = $this->request->post;
        }
        if(!empty($post)){
            $this->model_extension_module_d_ajax_search->save_statistic($post);
            $this->response->setOutput(json_encode('ok'));
        }
        $this->response->setOutput(json_encode('error'));
    }

    public function getAutocomplite(){
        if(isset($this->request->get['keyword'])){
            $keyword=$this->request->get['keyword'];
        }else{
            $keyword='';
        }

        $result=$this->model_extension_module_d_ajax_search->autocomplite($keyword);
        $this->response->setOutput(json_encode($result));
    }

    public function searchresults(){

        if(isset($this->request->get['keyword'])){
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

    protected function addScript($script) {
        $this->scripts[] = $script;
    }

    protected function addStyle($style) {
        $this->styles[] = $style;
    }

    protected function parseHeader($header)
    {
        $html_dom = new d_simple_html_dom();
        $html_dom->load($header, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        foreach ($this->scripts as $script) {
            if (!$html_dom->find('head', 0)->find('script[src="' . $script . '"]')) {
                if ($html_dom->find('head > script', -1)) {
                    $html_dom->find('head > script', -1)->outertext .= '<script src="' . $script . '" type="text/javascript"></script>';
                } else {
                    $html_dom->find('head', -1)->innertext .= '<script src="' . $script . '" type="text/javascript"></script>';
                    $html_dom->load((string)$html_dom, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
                }
            }
        }
        foreach ($this->styles as $style) {
            if (!$html_dom->find('head', 0)->find('link[href="' . $style . '"]')) {
                if ($html_dom->find('head > link', -1)) {
                    $html_dom->find('head > link', -1)->outertext .= '<link href="' . $style . '" rel="stylesheet" type="text/css"/>';
                } else {
                    $html_dom->find('head', -1)->innertext .= '<link href="' . $style . '" rel="stylesheet" type="text/css"/>';
                    $html_dom->load((string)$html_dom, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
                }
            }
        }
        return (string)$html_dom;
    }
}