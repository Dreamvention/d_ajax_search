<?php
/*
 * location: admin/controller
 */
class ControllerExtensionModuleDAjaxSearch extends Controller
{
    private $id = 'd_ajax_search';
    private $codename = 'd_ajax_search';
    private $route = 'extension/module/d_ajax_search';
    private $extension = '';
    private $config_file = 'd_ajax_search';
    private $store_id = 0;
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/module');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->language($this->route);

        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_opencart_patch = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_opencart_patch.json'));
        $this->d_twig_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_twig_manager.json'));
        $this->extension_plus =(file_exists(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->id.'_pro.json'));
        $this->d_event_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json'));
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->id.'.json'), true);
    }

    public function index()
    {
        $data = array();

        if($this->d_shopunity){
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->validateDependencies($this->codename);
        }

        if($this->d_twig_manager){
            $this->load->model('extension/module/d_twig_manager');
            if(!$this->model_extension_module_d_twig_manager->isCompatible()){
                $this->model_extension_module_d_twig_manager->installCompatibility();
                $this->session->data['success'] = $this->language->get('success_twig_compatible');
                $this->response->redirect($this->model_extension_d_opencart_patch_url->getExtensionLink('module'));
            }
        }
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            if (VERSION >= '3.0.0.0') {
                $ajs_post_array = array();
                if ($this->request->post[$this->codename.'_status'] == 0) {
                    $ajs_post_array['module_'.$this->codename.'_status'] = 0;
                } elseif ($this->request->post[$this->codename.'_status'] == 1) {
                    $ajs_post_array['module_'.$this->codename.'_status'] = 1;
                }

                $this->model_setting_setting->editSetting('module_'.$this->id, $ajs_post_array);
            }

            $this->model_setting_setting->editSetting($this->id, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->model_extension_d_opencart_patch_url->getExtensionLink('module'));
        }

        if (isset($this->request->post[$this->id.'_status'])) {
            $data[$this->id.'_status'] = $this->request->post[$this->id.'_status'];
        } else {
            $data[$this->id.'_status'] = $this->config->get($this->id.'_status');
        }


        $this->config->load('d_ajax_search');
        $this->config->get('d_ajax_search_setting');

        if ($this->model_setting_setting->getSetting($this->codename)) {
            $setting = $this->model_setting_setting->getSetting($this->codename);
        }else{
            $setting['d_ajax_search_setting']=$this->config->get('d_ajax_search_setting');
        }



        $this->document->addStyle('view/javascript/d_ajax_search/d_ajax_search.css');
        $this->document->addStyle('view/javascript/d_ajax_search/d_design.css');
        $this->document->addScript('view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle('view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');
        $this->document->addScript('view/javascript/d_ajax_search/jquery.tinysort.min.js');
        $this->document->addScript('view/javascript/d_rubaxa_sortable/sortable.js');
        $this->document->addStyle('view/javascript/d_rubaxa_sortable/sortable.css');
        $this->document->addScript('view/javascript/d_rubaxa_sortable/sortable.js');
        $this->document->addStyle('view/javascript/d_rubaxa_sortable/sortable.css');

        $data['setting'] = $setting['d_ajax_search_setting'];

        $url_token = '';

        if (isset($this->session->data['token'])) {
            $url_token .= 'token=' . $this->session->data['token'];
        }

        if (isset($this->session->data['user_token'])) {
            $url_token .= 'user_token=' . $this->session->data['user_token'];
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['url_token']=$url_token;

        $this->load->language($this->route);
        $data['version'] = $this->extension['version'];
        $data['id']=$this->codename;
        $data['entry_status']=$this->language->get('entry_status');
        $data['status_on']=$this->language->get('status_on');
        $data['status_off']=$this->language->get('status_off');
        $data['action'] = $this->model_extension_d_opencart_patch_url->link($this->route);
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->getExtensionLink('module');
        $data['token'] = $this->model_extension_d_opencart_patch_user->getUrlToken();

         $this->document->setTitle($this->language->get('heading_title_main'));
        if (!file_exists(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->id.'_pro.json')) {
            $data['info'] = $this->language->get('help_d_ajax_search_pack');
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->deleteEvent($this->codename);
        }
        $data['heading_title'] = $this->language->get('heading_title_main');
        // Tab
        $data['text_settings'] = $this->language->get('text_settings');
        $data['text_instructions'] = $this->language->get('text_instructions');
        $data['text_instructions_full'] = $this->language->get('text_instructions_full');

        // Button
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_get_update'] = $this->language->get('button_get_update');
        $data['more_details']=$this->language->get('more_details');

        // Entry
        $data['entry_get_update'] = sprintf($this->language->get('entry_get_update'), $data['version']);
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_width'] = $this->language->get('entry_width');
        $data['entry_max_symbols'] = $this->language->get('entry_max_symbols');
        $data['entry_max_results'] = $this->language->get('entry_max_results');
        $data['entry_first_symbols'] = $this->language->get('entry_first_symbols');
        $data['entry_priority'] = $this->language->get('entry_priority');
        $data['entry_price'] = $this->language->get('entry_price');
        $data['entry_special'] = $this->language->get('entry_special');
        $data['entry_tax'] = $this->language->get('entry_tax');
        $data['entry_model'] = $this->language->get('entry_model');
        $data['entry_class'] = $this->language->get('entry_class');
        $data['entry_extended'] = $this->language->get('entry_extended');
        $data['tooltip_suggestion'] = $this->language->get('tooltip_suggestion');
        $data['tooltip_smart_search'] = $this->language->get('tooltip_smart_search');
        $data['tooltip_autocomplete'] = $this->language->get('tooltip_autocomplete');


        // Text
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['text_module'] = $this->language->get('text_module');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_category'] = $this->language->get('text_category');
        $data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $data['text_information'] = $this->language->get('text_information');
        $data['text_blog_article'] = $this->language->get('text_blog_article');
        $data['text_blog_category'] = $this->language->get('text_blog_category');
        $data['text_on'] = $this->language->get('text_on');
        $data['text_off'] = $this->language->get('text_off');
        $data['text_px'] = $this->language->get('text_px');

        // Help
        $data['help_width'] = $this->language->get('help_width');
        $data['help_max_symbols'] = $this->language->get('help_max_symbols');
        $data['help_max_results'] = $this->language->get('help_max_results');
        $data['help_class'] = $this->language->get('help_class');
        $data['help_first_symbols'] = $this->language->get('help_first_symbols');
        $data['help_on_off'] = $this->language->get('help_on_off');
        $data['help_general_version'] = $this->language->get('help_general_version');
        $data['help_extended'] = $this->language->get('help_extended');


         $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_d_opencart_patch_url->getExtensionLink('module')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route)
        );
        $data['redirect'] = HTTP_SERVER . 'index.php?route=' . $this->route . '/editRedirect&'.$this->model_extension_d_opencart_patch_user->getUrlToken();
        $this->load->model('extension/module/d_ajax_search');
        $extensions = $this->model_extension_module_d_ajax_search->getExtensions();


//        $data['statistic'] = $this->model_extension_module_d_ajax_search->getStatistic();
//        $data['top_searches'] = $this->model_extension_module_d_ajax_search->getTopsearches();

        $data['hour'] = $this->url->link($this->route.'/updateCharts', $url_token . '&time=1', true);
        $data['week'] = $this->url->link($this->route.'/updateCharts', $url_token . '&time=7', true);
        $data['mounth'] = $this->url->link($this->route.'/updateCharts', $url_token . '&time=30', true);
        $data['year'] = $this->url->link($this->route.'/updateCharts', $url_token . '&time=365', true);

        // $url='';

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        }

        $filter_data = array(
            'keyword' => isset($filter_name) ? $filter_name : '',
            'start' => ($page - 1) * 10,
            'limit' => 10
        );

        $allHistory = $this->model_extension_module_d_ajax_search->allHistory();
        $data['histories']    = $this->model_extension_module_d_ajax_search->getHistory($filter_data);

        $pagination = new Pagination();
        $pagination->total = count($allHistory);
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link($this->route, $url_token . '&page={page}', true);

        $data['pagination'] = $pagination->render();

       $data['results'] = sprintf($this->language->get('text_pagination'), (count($allHistory)) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > (count($allHistory) - 10)) ? count($allHistory) : ((($page - 1) * 10) + 10), count($allHistory), ceil(count($allHistory) / 10));


        $setting = $this->model_setting_setting->getSetting($this->codename);
        $setting = (isset($setting[$this->codename.'_setting'])) ? $setting[$this->codename.'_setting'] : array();
        if($this->extension_plus){$data['extension_plus']=1;}
        if ($extensions) {
            $this->load->model('user/user_group');
            foreach ($extensions as $extension) {

                $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'access', 'extension/'.$this->codename.'/'.$extension);
                $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'modify', 'extension/'.$this->codename.'/'.$extension);

                $this->config->load('d_ajax_search/'.$extension);
                $queries=$this->config->get('d_ajax_search_'.$extension);
                    $data['extensions'][$extension] = array(
                        'id' => $extension,
                        'enabled' => (isset($setting['extension'][$extension]['enabled'])) ? $setting['extension'][$extension]['enabled'] : true,
                        'sort_order' => (isset($setting['extension'][$extension]['sort_order'])) ? $setting['extension'][$extension]['sort_order'] : 10000,
                        'max_count' => (isset($setting['extension'][$extension]['max_count'])) ? $setting['extension'][$extension]['max_count'] : 7,
                    );

                    foreach ($queries['query'] as $key => $query) {
                        if (isset($query['tooltip'])) {
                            $data['extensions'][$extension]['query'][$key]['tooltip']=$query['tooltip'];
                        }
                    $data['extensions'][$extension]['query'][$key]['status']=(isset($setting['extension'][$extension]['query'][$key])) ? $setting['extension'][$extension]['query'][$key] : 1;

                }
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/module/d_ajax_search', $data));
    }

    public function updateCharts()
    {

        if (isset($this->request->get['time'])) {
            $time = $this->request->get['time'];
        } else {
            $time = 1;
        }
        $data['statistic'] = $this->model_extension_module_d_ajax_search->getStatistic($time);
        $data['top_searches'] = $this->model_extension_module_d_ajax_search->getTopsearches($time);
        // FB::log($time);
        $json=$data;
        $this->response->setOutput(json_encode($json));
    }

    public function editRedirect(){
        if(isset($this->request->post['query_id'])){
            $this->model_extension_module_d_ajax_search->updateRedirect($this->request->post['query_id'],$this->request->post['value']);
            $json['value']=$this->request->post['value'];
            $this->response->setOutput(json_encode($json));
        }else{
            $this->response->setOutput(json_encode('error'));
        }

    }

    public function install()
    {
        if ($this->d_shopunity) {
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies($this->codename);
        }

        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'access', 'extension/'.$this->codename);
        $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'modify', 'extension/'.$this->codename);

        if ($this->d_opencart_patch) {
            $this->load->model('extension/d_opencart_patch/modification');
            $this->model_extension_d_opencart_patch_modification->setModification('d_ajax_search.xml', 1);
            $this->model_extension_d_opencart_patch_modification->refreshCache();
        }

        $this->model_extension_module_d_ajax_search->createDatabase();

        if ($this->d_event_manager) {
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/view/customer/customer_form/after', 'extension/module/d_ajax_search/view_customer_customer_form_after');
        }
    }

    public function uninstall()
    {
        if ($this->d_opencart_patch) {
            $this->load->model('extension/d_opencart_patch/modification');
            $this->model_extension_d_opencart_patch_modification->setModification('d_ajax_search.xml', 0);
            $this->model_extension_d_opencart_patch_modification->refreshCache();
        }

        if (file_exists(DIR_APPLICATION . 'model/extension/module/d_event_manager.php')) {
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->deleteEvent($this->codename);
        }

        $this->model_extension_module_d_ajax_search->dropDatabase();
    }

        protected function validate()
    {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['config'])) {
            return false;
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function view_customer_customer_form_after(&$route, &$data, &$output){
        $url_token='';

        if (isset($this->session->data['token'])) {
            $url_token .= 'token=' . $this->session->data['token'];
        }

        if (isset($this->session->data['user_token'])) {
            $url_token .= 'user_token=' . $this->session->data['user_token'];
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $html_dom = new d_simple_html_dom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $html_dom->find('#form-customer .nav-tabs', 0)->innertext .= '<li><a href="#tab-customer-search-history" data-toggle="tab">Search History</a></li>';


        $data=array();


        $filter_data = array(
            'customer_id' => isset($this->request->get['customer_id']) ? $this->request->get['customer_id'] : '',
            'url_token' => $url_token,
            'start' => ($page - 1) * 20,
            'limit' => 20
        );

        $allHistory = $this->model_extension_module_d_ajax_search->allHistory();
        $data['histories']    = $this->model_extension_module_d_ajax_search->getCustomerHistory($filter_data);

        $pagination = new Pagination();
        $pagination->total = count($allHistory);
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link($this->route, $url_token . '&page={page}', true);

        $data['pagination'] = $pagination->render();

       $data['results'] = sprintf($this->language->get('text_pagination'), (count($allHistory)) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > (count($allHistory) - 10)) ? count($allHistory) : ((($page - 1) * 10) + 10), count($allHistory), ceil(count($allHistory) / 10));

        $html_dom->find('#form-customer .tab-content', 0)->innertext .= $this->model_extension_d_opencart_patch_load->view('extension/d_ajax_search/customer_search_history', $data);


        $output = (string)$html_dom;
    }
}