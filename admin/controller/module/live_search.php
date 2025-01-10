<?php
namespace Opencart\Admin\Controller\Extension\LiveSearch\Module;

class LiveSearch extends \Opencart\System\Engine\Controller
{
    private $codename = 'live_search';
    private $route = 'extension/live_search/module/live_search';
    private $config_file = 'live_search';
    private $store_id = 0;
    private $error = array();
	private $extension;

    private $opencart_patch;

    public function __construct($registry)
    {
        parent::__construct($registry);
		$this->extension = json_decode(file_get_contents(DIR_EXTENSION . 'live_search/install.json'), true);
        $this->opencart_patch = is_file(DIR_EXTENSION . 'dv_opencart_patch/install.json');
        $this->load->model($this->route);
        $this->model_extension_live_search_module_live_search->loadDependencies();
    }

    public function index()
    {

        $this->load->language($this->route);

        $this->load->model('setting/setting');


        $this->document->addStyle(HTTP_CATALOG. 'extension/live_search/admin/view/stylesheet/d_bootstrap_extra/bootstrap.css');
        $this->document->addScript(HTTP_CATALOG. 'extension/live_search/admin/view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle(HTTP_CATALOG. 'extension/live_search/admin/view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');

        $data['url_extension'] = HTTP_CATALOG . 'extension/live_search/admin/';

        $this->document->addScript(HTTP_CATALOG . 'extension/live_search/admin/view/javascript/d_tinysort/tinysort.min.js');
        $this->document->addScript(HTTP_CATALOG . 'extension/live_search/admin/view/javascript/d_tinysort/tinysort.min.js');
        $this->document->addStyle(HTTP_CATALOG . 'extension/live_search/admin/view/javascript/d_tinysort/tinysort.min.js');
        $this->document->addScript(HTTP_CATALOG . 'extension/live_search/admin/view/javascript/d_tinysort/tinysort.min.js');

        $this->document->addStyle(HTTP_CATALOG . 'extension/live_search/admin/view/javascript/d_tinysort/tinysort.min.js');

        $data = array();

        $data['pro'] = is_file(DIR_EXTENSION . 'live_search_pro/install.json');

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

        $data['non_installed'] = $this->getNonInstalledDependencies();
		if ($data['non_installed']) {
			$data['location'] = html_entity_decode($this->url->link('extension/live_search/module/'.$this->codename, 'user_token=' . $this->session->data['user_token']));
			$data['user_token'] = $this->session->data['user_token'];
			$data['non_installed'] = json_encode($data['non_installed']);
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('extension/live_search/module/dependency_installer', $data));
            return ;
        }

        $this->load->model('extension/dv_opencart_patch/library/url');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if ($this->request->post[$this->codename.'_status'] == 0) {
                $this->uninstallEvents();
            } elseif ($this->request->post[$this->codename.'_status'] == 1) {
                $this->installEvents();
            }

            $this->model_setting_setting->editSetting($this->codename, $this->request->post);


            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->model_extension_dv_opencart_patch_library_url->linkWithToken('marketplace/extension', '&type=module', true));
        }

        $data['action'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route);
        $data['cancel'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken('marketplace/extension', ['type' => 'module'], true);

        $data['setup'] = $this->isSetup();
        $data['setup_link'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route.'|setup', '', true);


        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_dv_opencart_patch_library_url->linkWithToken('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_dv_opencart_patch_library_url->linkWithToken('marketplace/extension', ['type' => 'module'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route)
        );

        if (isset($this->request->post[$this->codename.'_status'])) {
            $data[$this->codename.'_status'] = $this->request->post[$this->codename.'_status'];
        } else {
            $data[$this->codename.'_status'] = $this->config->get($this->codename.'_status');
        }

        $data['setting'] = $this->getSetting();

        $this->load->model($this->route);
        if($this->isSetup()){
            $extensions = $this->model_extension_live_search_module_live_search->getExtensions();

            $data['statistic'] = $this->model_extension_live_search_module_live_search->getStatistic();
            $data['top_searches'] = $this->model_extension_live_search_module_live_search->getTopsearches();
        }

        $data['languages'] = array();

        $this->load->model('localisation/language');

        $results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name' => $result['name'],
					'code' => $result['code']
				);
			}
		}

        foreach ($data['languages'] as $language) {
			if (isset($this->request->post['live_search_rtl'])) {
                $rtl = json_decode($this->request->post['live_search_rtl']);
                $data['live_search_rtl'.'['.$language['code'].']'] = $rtl[$language['code']];
            }elseif ($this->config->get('live_search_rtl')) {
                $rtl = $this->config->get('live_search_rtl');
                $data['live_search_rtl'.'['.$language['code'].']'] = (isset($rtl[$language['code']]) ? $rtl[$language['code']] : 0);
            }else {
                $data['live_search_rtl'.'['.$language['code'].']'] = 0;
            }
		}
        $data['hour'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route.'|updateCharts','time=1');
        $data['week'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route.'|updateCharts','time=7');
        $data['mounth'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route.'|updateCharts','time=30');
        $data['year'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route.'|updateCharts', 'time=365');

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        }

        $filter_data = array(
            'keyword' => isset($filter_name) ? $filter_name : '',
            'start' => ($page - 1) * 10,
            'limit' => 10
        );
        if($this->isSetup()){
            $data['histories'] = $this->model_extension_live_search_module_live_search->getHistory($filter_data);
        }
        $data['redirect'] = $this->model_extension_dv_opencart_patch_library_url->linkWithToken($this->route . '|editRedirect');

        $setting = $this->model_setting_setting->getSetting($this->codename);

        $setting = (isset($setting['module_'.$this->codename.'_setting'])) ? $setting['module_'.$this->codename.'_setting'] : array();
        if (!empty($extensions)) {
            $this->load->model('user/user_group');
            foreach ($extensions as $extension) {

                $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/live_search/'.$this->codename.'/'.$extension);
                $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/live_search/'.$this->codename.'/'.$extension);
                if (is_file(DIR_OPENCART . 'extension/live_search/system/config/live_search/'.$extension.'.php')) {
                    $this->config->addPath(DIR_OPENCART . 'extension/live_search/system/config/');
                    $this->config->load('live_search/'.$extension);
                    $queries=$this->config->get('live_search_'.$extension);

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
                } else {
                    $this->config->addPath(DIR_OPENCART . 'extension/live_search_pro/system/config/');
                    $this->config->load('live_search/'.$extension);
                    $queries=$this->config->get('live_search_'.$extension);

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
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view($this->route, $data));
    }

    public function getSetting(){
        $key = $this->codename.'_setting';

        if ($this->config_file) {
            $this->config->addPath(DIR_OPENCART . 'extension/live_search/system/config/');
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

            if (isset($setting[$key]) && is_array($setting[$key])) {
                foreach ($setting[$key] as $key => $value) {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    public function isSetup() {
        $this->load->model('setting/setting');
        $setting_module = $this->model_setting_setting->getSetting($this->codename);
        if(!$setting_module) {
            return false;
        }
        return true;
    }

    public function setup(){
        //install
        $this->load->model($this->route);
        $this->model_extension_live_search_module_live_search->createDatabase();
        $this->config->addPath(DIR_OPENCART . 'extension/live_search/system/config/');
        $this->config->load('live_search');

        $this->load->model('setting/event');
        $this->installEvents();



        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/'.$this->codename);
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/'.$this->codename);
        ///

        $this->load->language($this->route);
        $this->load->model('setting/setting');

        $this->load->model($this->route);
        $this->config->addPath('extension/live_search/system/config');
        $this->config->load('live_search');
        $setting = $this->config->get('live_search_setting');
        if(!is_array($setting)){
            $setting = json_decode($setting, true);
        }

        $extensions = $this->model_extension_live_search_module_live_search->getExtensions();
        if ($extensions) {
            $this->load->model('user/user_group');
            foreach ($extensions as $extension) {

                $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/'.$this->codename.'/'.$extension);
                $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/'.$this->codename.'/'.$extension);
                if (is_file(DIR_OPENCART . 'extension/live_search/system/config/live_search/'.$extension.'.php')) {
                    $this->config->addPath(DIR_OPENCART . 'extension/live_search/system/config/');
                    $this->config->load('live_search/'.$extension);
                    $queries=$this->config->get('live_search_'.$extension);

                    $setting['extension'][$extension] = array(
                        'id' => $extension,
                        'enabled' =>  true,
                        'sort_order' => 0,
                        'max_count' => 7,
                    );

                    foreach ($queries['query'] as $key => $query) {
                        $setting['extension'][$extension]['query'][$key]=  1;
                    }
                } else {
                    $this->config->addPath(DIR_OPENCART . 'extension/live_search_pro/system/config/');
                    $this->config->load('live_search/'.$extension);
                    $queries=$this->config->get('live_search_'.$extension);

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
        }
        //4.x
        $new_post = ($setting ? $setting : []);
        $new_post[$this->codename.'_status'] = 1;
        $new_post[$this->codename.'_setting'] = $setting;
        $this->model_setting_setting->editSetting($this->codename, $new_post);

        $this->session->data['success'] = $this->language->get('success_setup');
        $this->response->redirect($this->url->link($this->route, 'user_token='.$this->session->data['user_token']));
    }

    public function install()
    {

    }

    public function uninstall()
    {
        $this->uninstallEvents();
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting($this->codename);

        $this->load->model('extension/live_search/module/live_search');
        $this->model_extension_live_search_module_live_search->dropDatabase();
    }

    public function installEvents() {
        if ($this->opencart_patch) {
            $this->load->model('extension/dv_opencart_patch/setting/event');

            $this->model_extension_dv_opencart_patch_setting_event->deleteEventByCode($this->codename);

            $this->config->addPath(DIR_OPENCART . 'extension/live_search/system/config/');
            $this->config->load('live_search');

            $events = $this->config->get('live_search_events');
            foreach ($events as $event) {
                $this->model_extension_dv_opencart_patch_setting_event->addEvent($event);
            }
        }
    }

    public function uninstallEvents() {
        if ($this->opencart_patch) {
            $this->load->model('extension/dv_opencart_patch/setting/event');

            $this->model_extension_dv_opencart_patch_setting_event->deleteEventByCode($this->codename);
        }
    }

    private function getNonInstalledDependencies($dependencies = []) {
		$dependencies = $dependencies ? $dependencies : array_keys($this->extension['dependencies']);
		$this->load->model('setting/extension');
		$non_installed = [];
		foreach ($dependencies as $dependency) {
			$install = $this->model_setting_extension->getInstallByCode($dependency);
			if (empty($install['status'])) {
				$non_installed[] = $dependency;
			} else {
				$dependency_extension = json_decode(file_get_contents(DIR_EXTENSION . $dependency . '/install.json'), 1);
				if (!empty($dependency_extension['dependencies'])) {
					$non_installed = array_merge($non_installed, $this->getNonInstalledDependencies(array_keys($dependency_extension['dependencies'])));
				}
			}
		}

		return $non_installed;
	}

    public function installDependencies() {
		$this->load->language('marketplace/installer');

		$json = [];

		$code = $this->request->get['code'] ?? '';

		$page = $this->request->get['page'] ?? 1;

		$this->load->model('setting/extension');

		$extension_install_info = $code ? $this->model_setting_extension->getInstallByCode($code) : [];

		if ($extension_install_info) {
			$file = DIR_STORAGE . 'marketplace/' . $extension_install_info['code'] . '.ocmod.zip';

			if (!is_file($file)) {
				$json['error'] = sprintf($this->language->get('error_file'), $extension_install_info['code'] . '.ocmod.zip');
			}

			if ((int)$page > 1 && !is_dir(DIR_EXTENSION . $extension_install_info['code'] . '/')) {
				$json['error'] = sprintf($this->language->get('error_directory'), $extension_install_info['code'] . '/');
			}
		} else {
			$json['error'] = $this->language->get('error_extension');
		}

		if (!$json) {
			// Unzip the files
			$zip = new \ZipArchive();

			if ($zip->open($file)) {
				$total = $zip->numFiles;
				$limit = 200;

				$start = ((int)$page - 1) * $limit;
				$end = $start > ($total - $limit) ? $total : ($start + $limit);

				// Check if any of the files already exist.
				for ($i = $start; $i < $end; $i++) {
					$source = $zip->getNameIndex($i);

					$destination = str_replace('\\', '/', $source);

					// Only extract the contents of the upload folder
					$path = $extension_install_info['code'] . '/' . $destination;
					$base = DIR_EXTENSION;
					$prefix = '';

					// image > image
					if (substr($destination, 0, 6) == 'image/') {
						$path = $destination;
						$base = substr(DIR_IMAGE, 0, -6);
					}

					// We need to store the path differently for vendor folders.
					if (substr($destination, 0, 15) == 'system/storage/') {
						$path = substr($destination, 15);
						$base = DIR_STORAGE;
						$prefix = 'system/storage/';
					}

					// Must not have a path before files and directories can be moved
					$path_new = '';

					$directories = explode('/', dirname($path));

					foreach ($directories as $directory) {
						if (!$path_new) {
							$path_new = $directory;
						} else {
							$path_new = $path_new . '/' . $directory;
						}

						// To fix storage location
						if (!is_dir($base . $path_new . '/') && mkdir($base . $path_new . '/', 0777)) {
							$this->model_setting_extension->addPath($extension_install_info['extension_install_id'], $prefix . $path_new);
						}
					}

					// If check if the path is not directory and check there is no existing file
					if (substr($source, -1) != '/') {
						if (!is_file($base . $path) && copy('zip://' . $file . '#' . $source, $base . $path)) {
							$this->model_setting_extension->addPath($extension_install_info['extension_install_id'], $prefix . $path);
						}
					}
				}

				$zip->close();
			} else {
				$json['error'] = $this->language->get('error_unzip');
			}
		}

		if (!$json) {
			$json['text'] = sprintf($this->language->get('text_progress'), 2, $total);

			$url = '';

			$url .= '&code=' . $code;


			if (((int)$page * 200) <= $total) {
                if (VERSION > '4.0.1.1') {
                    $json['next'] = $this->url->link('extension/live_search/module/live_search.installDependencies', 'user_token=' . $this->session->data['user_token'] . $url . '&page=' . ((int)$page + 1), true);
                } else {
                    $json['next'] = $this->url->link('extension/live_search/module/live_search|installDependencies', 'user_token=' . $this->session->data['user_token'] . $url . '&page=' . ((int)$page + 1), true);
                }

			} else {
                if (VERSION > '4.0.1.1') {
                    $this->load->controller('marketplace/installer.vendor');
                } else {
                    $this->load->controller('marketplace/installer|vendor');
                }


				$output = json_decode($this->response->getOutput(), 1);

				$json = array_merge($json, $output);

				$extension = json_decode(file_get_contents(DIR_EXTENSION . $code . '/install.json'), 1);
				if (!empty($extension['dependencies'])) {
					$json['dependencies'] = array_keys($extension['dependencies']);
				} else if ($this->getNonInstalledDependencies()) {
                    $this->opencart_patch = is_file(DIR_EXTENSION . 'dv_opencart_patch/install.json');
                    $this->install();
                }
				$this->model_setting_extension->editStatus($extension_install_info['extension_install_id'], 1);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
        $this->load->model('extension/live_search/module/live_search');
        $data['statistic'] = $this->model_extension_live_search_module_live_search->getStatistic($time);
        $data['top_searches'] = $this->model_extension_live_search_module_live_search->getTopsearches($time);

        $json=$data;
        $this->response->setOutput(json_encode($json));
    }

    public function editRedirect(){
        if(isset($this->request->post['query_id'])){
            $this->model_extension_live_search_module_live_search->updateRedirect($this->request->post['query_id'],$this->request->post['value']);
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

        $html_dom = new \Opencart\System\Library\Extension\DvSimpleHtmlDom\DvSimpleHtmlDom();
        $html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $html_dom->find('#form-customer .nav-tabs', 0)->innertext .= '<li class="nav-item"><a class="nav-link" href="#tab-customer-search-history" data-bs-toggle="tab">Search History</a></li>';


        $data=array();


        $filter_data = array(
            'customer_id' => isset($this->request->get['customer_id']) ? $this->request->get['customer_id'] : '',
            'url_token' => $url_token,
            'start' => ($page - 1) * 20,
            'limit' => 20
        );
        $this->load->model($this->route);

        $allHistory = $this->model_extension_live_search_module_live_search->allHistory();
        $data['histories']    = $this->model_extension_live_search_module_live_search->getCustomerHistory($filter_data);

        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => count($allHistory),
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'] . 'page={page}')
		]);

        $data['results'] = sprintf($this->language->get('text_pagination'), (count($allHistory)) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > (count($allHistory) - 10)) ? count($allHistory) : ((($page - 1) * 10) + 10), count($allHistory), ceil(count($allHistory) / 10));

        $html_dom->find('#form-customer .tab-content', 0)->innertext .= $this->load->view('extension/live_search/live_search/customer_search_history', $data);


        $output = (string)$html_dom;
    }
}
