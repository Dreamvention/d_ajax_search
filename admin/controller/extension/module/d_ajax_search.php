<?php
/*
 * location: admin/controller
 */
class ControllerExtensionModuleDAjaxSearch extends Controller
{
    private $codename = 'd_ajax_search';
    private $route = 'extension/module/d_ajax_search';
    private $config_file = 'd_ajax_search';
    private $store_id = 0;
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_opencart_patch = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_opencart_patch.json'));
        $this->d_twig_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_twig_manager.json'));
        $this->d_ajax_search_pro =(file_exists(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'_pro.json'));
        $this->d_event_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json'));
        $this->d_admin_style = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
        $this->load->model('extension/module/d_ajax_search');
    }

    public function index()
    {

        if($this->d_shopunity){
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->validateDependencies($this->codename);
        }

        if($this->d_twig_manager){
            $this->load->model('extension/module/d_twig_manager');
            $this->model_extension_module_d_twig_manager->installCompatibility();
        }

        if($this->d_event_manager){
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->installCompatibility();
        }

        if ($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');
            $this->model_extension_d_admin_style_style->getStyles('light');
        }

        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/module');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/cache');

        $this->model_extension_d_opencart_patch_cache->clearTwig();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            if (VERSION >= '3.0.0.0') {
                $new_post = array();
                if ($this->request->post[$this->codename.'_status'] == 0) {
                    $new_post['module_'.$this->codename.'_status'] = 0;
                } elseif ($this->request->post[$this->codename.'_status'] == 1) {
                    $new_post['module_'.$this->codename.'_status'] = 1;
                }
                $this->model_setting_setting->editSetting('module_'.$this->codename, $new_post);
            }

            $this->model_setting_setting->editSetting($this->codename, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->model_extension_d_opencart_patch_url->getExtensionLink('module'));
        }

        
        $this->document->addScript('view/javascript/d_tinysort/tinysort.min.js');
        $this->document->addScript('view/javascript/d_tinysort/jquery.tinysort.min.js');
        $this->document->addStyle('view/javascript/d_rubaxa_sortable/sortable.css');
        $this->document->addScript('view/javascript/d_rubaxa_sortable/sortable.js');

        $this->document->addStyle('view/stylesheet/d_ajax_search/d_ajax_search.css');

        $data = array();

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');

        $data['codename'] = $this->codename;
        $data['route'] = $this->route;
        $data['version'] = $this->extension['version'];
        $data['token'] =  $this->model_extension_d_opencart_patch_user->getToken();
        $data['pro'] = $this->d_ajax_search_pro;
        $data['d_shopunity'] = $this->d_shopunity;
        $data['store_id'] = $this->store_id;

        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();
        $data['url_token'] = $this->model_extension_d_opencart_patch_user->getUrlToken();
        
        // Tab
        $data['text_settings'] = $this->language->get('text_settings');
        $data['text_instructions'] = $this->language->get('text_instructions');

        $data['text_pro'] = $this->language->get('text_pro');

        // Button
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['more_details'] = $this->language->get('more_details');

        // Entry
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
        $data['priority_search'] = $this->language->get('priority_search');
        $data['filter_keyword'] = $this->language->get('filter_keyword');

        $data['where_was_found'] = $this->language->get('where_was_found');
        $data['display_results_in_block'] = $this->language->get('display_results_in_block');
        $data['display_unic_image'] = $this->language->get('display_unic_image');
        $data['result_image_size'] = $this->language->get('result_image_size');
        $data['show_more_results_link'] = $this->language->get('show_more_results_link');

        $data['text_24hour'] = $this->language->get('text_24hour');
        $data['text_week'] = $this->language->get('text_week');
        $data['text_month'] = $this->language->get('text_month');
        $data['text_year'] = $this->language->get('text_year');

        // Help
        $data['help_width'] = $this->language->get('help_width');
        $data['help_max_symbols'] = $this->language->get('help_max_symbols');
        $data['help_max_results'] = $this->language->get('help_max_results');
        $data['help_class'] = $this->language->get('help_class');
        $data['help_first_symbols'] = $this->language->get('help_first_symbols');
        $data['help_on_off'] = $this->language->get('help_on_off');
        $data['help_general_version'] = $this->language->get('help_general_version');
        $data['help_extended'] = $this->language->get('help_extended');


        $data['action'] = $this->model_extension_d_opencart_patch_url->link($this->route);
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->getExtensionLink('module');

        $data['setup'] = $this->isSetup();
        $data['text_setup'] = $this->language->get('text_setup');
        $data['setup_link'] = $this->model_extension_d_opencart_patch_url->ajax($this->route.'/setup');


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

        if (isset($this->request->post[$this->codename.'_status'])) {
            $data[$this->codename.'_status'] = $this->request->post[$this->codename.'_status'];
        } else {
            $data[$this->codename.'_status'] = $this->config->get($this->codename.'_status');
        }

        $data['setting'] = $this->getSetting();

        $this->load->model('extension/module/d_ajax_search');
        $extensions = $this->model_extension_module_d_ajax_search->getExtensions();


//        $data['statistic'] = $this->model_extension_module_d_ajax_search->getStatistic();
//        $data['top_searches'] = $this->model_extension_module_d_ajax_search->getTopsearches();

        $data['hour'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/updateCharts','time=1');
        $data['week'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/updateCharts','time=7');
        $data['mounth'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/updateCharts','time=30');
        $data['year'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/updateCharts', 'time=365');

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
        $pagination->url = $this->model_extension_d_opencart_patch_url->link($this->route, 'page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), (count($allHistory)) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > (count($allHistory) - 10)) ? count($allHistory) : ((($page - 1) * 10) + 10), count($allHistory), ceil(count($allHistory) / 10));

        $data['redirect'] = HTTP_SERVER . 'index.php?route=' . $this->route . '/editRedirect&'.$this->model_extension_d_opencart_patch_user->getUrlToken();

        $setting = $this->model_setting_setting->getSetting($this->codename);
        $setting = (isset($setting[$this->codename.'_setting'])) ? $setting[$this->codename.'_setting'] : array();
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

    public function getSetting(){
        $key = $this->codename.'_setting';

        if ($this->config_file) {
            $this->config->load($this->config_file);
        }

        $result = ($this->config->get($key)) ? $this->config->get($key) : array();

        if (!isset($this->request->post['config'])) {

            $this->load->model('setting/setting');
            if (isset($this->request->post[$key])) {
                $setting = $this->request->post;

            } elseif ($this->model_setting_setting->getSetting($this->codename, $this->store_id)) {
                $setting = $this->model_setting_setting->getSetting($this->codename, $this->store_id);

            }

            if (isset($setting[$key])) {
                foreach ($setting[$key] as $key => $value) {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    public function isSetup() {
        $this->load->model('extension/d_opencart_patch/extension');
        if(!$this->model_extension_d_opencart_patch_extension->isInstalled($this->codename)) {
            return false;
        }
        $this->load->model('setting/setting');
        $setting_module = $this->model_setting_setting->getSetting($this->codename);
        if(!$setting_module) {
            return false;
        }
        return true;
    }

    public function setup(){
        $this->load->language($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model($this->route);
        $this->config->load('d_ajax_search');
        $setting = $this->config->get('d_ajax_search_setting');

        $extensions = $this->model_extension_module_d_ajax_search->getExtensions();
        if ($extensions) {
            $this->load->model('user/user_group');
            foreach ($extensions as $extension) {

                $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'access', 'extension/'.$this->codename.'/'.$extension);
                $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'modify', 'extension/'.$this->codename.'/'.$extension);

                $this->config->load('d_ajax_search/'.$extension);
                $queries=$this->config->get('d_ajax_search_'.$extension);

                $setting['extension'][$extension] = array(
                    'id' => $extension,
                    'enabled' =>  true,
                    'sort_order' => 0,
                    'max_count' => 7,
                );

                foreach ($queries['query'] as $key => $query) {
                    $setting['extension'][$extension]['query'][$key]=  1;
                }
            }
        }

        //2.x
        $data = array();
        $data[$this->codename.'_status'] = 1;
        $data[$this->codename.'_setting'] = $setting;
        $this->model_setting_setting->editSetting($this->codename, $data);
        //3.x
        $new_post = array();
        $new_post['module_'.$this->codename.'_status'] = 1;
        $new_post['module_'.$this->codename.'_setting'] = $setting;
        $this->model_setting_setting->editSetting('module_'.$this->codename, $new_post);

        $this->session->data['success'] = $this->language->get('success_setup');
        $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route));
    }

    public function install()
    {
        if ($this->d_shopunity) {
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies($this->codename);
        }

        $this->load->model('extension/module/d_ajax_search');
        $this->model_extension_module_d_ajax_search->createDatabase();

        if ($this->d_event_manager) {
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/view/customer/customer_form/after', 'extension/module/d_ajax_search/view_customer_customer_form_after');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/view/common/header/after', 'extension/module/d_ajax_search/view_common_header_after');
        }

        if ($this->d_opencart_patch) {
            $this->load->model('user/user_group');
            $this->load->model('extension/d_opencart_patch/user');
            $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'access', 'extension/'.$this->codename);
            $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'modify', 'extension/'.$this->codename);

            $this->load->model('extension/d_opencart_patch/modification');
            $this->model_extension_d_opencart_patch_modification->setModification('d_ajax_search.xml', 1);
            $this->model_extension_d_opencart_patch_modification->refreshCache();
        }
        
    }

    public function uninstall()
    {
        if ($this->d_opencart_patch) {
            $this->load->model('extension/d_opencart_patch/modification');
            $this->model_extension_d_opencart_patch_modification->setModification('d_ajax_search.xml', 0);
            $this->model_extension_d_opencart_patch_modification->refreshCache();
        }

        if ($this->d_event_manager) {
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->deleteEvent($this->codename);
        }

        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting($this->codename);

        $this->load->model('extension/module/d_ajax_search');
        $this->model_extension_module_d_ajax_search->dropDatabase();
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function updateCharts()
    {

        if (isset($this->request->get['time'])) {
            $time = $this->request->get['time'];
        } else {
            $time = 1;
        }
        $this->load->model('extension/module/d_ajax_search');
        $data['statistic'] = $this->model_extension_module_d_ajax_search->getStatistic($time);
        $data['top_searches'] = $this->model_extension_module_d_ajax_search->getTopsearches($time);

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
        $this->load->model($this->route);
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/module/d_ajax_search');

        $allHistory = $this->model_extension_module_d_ajax_search->allHistory();
        $data['histories']    = $this->model_extension_module_d_ajax_search->getCustomerHistory($filter_data);

        $pagination = new Pagination();
        $pagination->total = count($allHistory);
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->model_extension_d_opencart_patch_url->link($this->route, 'page={page}');

        $data['pagination'] = $pagination->render();

       $data['results'] = sprintf($this->language->get('text_pagination'), (count($allHistory)) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > (count($allHistory) - 10)) ? count($allHistory) : ((($page - 1) * 10) + 10), count($allHistory), ceil(count($allHistory) / 10));

        $html_dom->find('#form-customer .tab-content', 0)->innertext .= $this->model_extension_d_opencart_patch_load->view('extension/d_ajax_search/customer_search_history', $data);


        $output = (string)$html_dom;
    }
}