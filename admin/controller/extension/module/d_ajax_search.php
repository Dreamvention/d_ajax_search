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

        $this->load->model('extension/module/d_ajax_search');
        $extensions = $this->model_extension_module_d_ajax_search->getExtensions();
        $data['statistic'] = $this->model_extension_module_d_ajax_search->getStatistic();
        $data['top_searches'] = $this->model_extension_module_d_ajax_search->getTopsearches();
        $data['histories']    = $this->model_extension_module_d_ajax_search->getHistory();
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

    public function install()
    {
        if ($this->d_shopunity) {
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies($this->codename);
        }

        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'access', 'extension/'.$this->codename);
        $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'modify', 'extension/'.$this->codename);

        $this->load->model('extension/d_opencart_patch/modification');
        $this->model_extension_d_opencart_patch_modification->setModification('d_ajax_search.xml', 1);
        $this->model_extension_d_opencart_patch_modification->refreshCache();
        $this->model_extension_module_d_ajax_search->createDatabase();
    }

    public function uninstall()
    {
        $this->load->model('extension/d_opencart_patch/modification');
        $this->model_extension_d_opencart_patch_modification->setModification('d_ajax_search.xml', 0);
        $this->model_extension_d_opencart_patch_modification->refreshCache();
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
}