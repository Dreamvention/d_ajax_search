<?php
class ModelExtensionModuleDAjaxSearch extends Model {
    private $id = 'd_ajax_search';
    private $route = 'extension/module/d_ajax_search';
    public function search($text, $searches = array(), $research=0) {
        $this->load->language($this->route);
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/information');
        $this->load->model('tool/image');
        $search_filter = array();
        $setting1      = $this->model_setting_setting->getSetting($this->id);

        $settings = $setting1['d_ajax_search_setting'];
        foreach ($searches as $search) {
            $this->load->config('d_ajax_search/' . $search);

            $search_filter[$search] = $this->config->get('d_ajax_search_' . $search);

        }
        $sql_array = array();
        $sql       = '';
        $suggestion='';
        $redirect=0;
        $settings['no_dublicate_images'] = isset($settings['no_dublicate_images']) ? $settings['no_dublicate_images'] : 0;
        $settings['suggestion'] = isset($settings['suggestion']) ? $settings['suggestion'] : 0;

        $sql_redirect="SELECT * FROM " . DB_PREFIX . "as_query WHERE text = '" . $text . "'";
        $query=$this->db->query($sql_redirect);
        if(!empty($query->rows[0]['redirect'])){
            $text=$query->rows[0]['redirect'];
            $redirect_text=$query->rows[0]['redirect'];
            $redirect=1;
        }

        if($research && $settings['suggestion']){
            $sql_smart="SELECT text FROM `" . DB_PREFIX . "as_query` ORDER BY count DESC LIMIT 100";
            $query=$this->db->query($sql_smart);
            $gml=0;
            $lev=20;
            $new_text='';
            foreach ($query->rows as $key => $row) {

// LEVENSTEIN
// if(levenshtein($text, $row['text']) < $lev){
//     $new_text=$row['text'];
//     $lev=levenshtein($text, $row['text']);

// }
// Oliver's algoritm

                similar_text( $text , $row['text'], $percent);
                if($percent > $gml && $percent > 65){
                    $new_text=$row['text'];
                    $saggestion= $row['text'];
                    $gml=$percent;
                }
            }

            if(!empty($new_text)){
                $text=$new_text;
            }
        }
// }
        $keywords=explode(' ', trim(preg_replace('/\s+/', ' ', $text)));

        foreach ($search_filter as $search => $filter) {

            $sql = 'SELECT ' . $filter['table']['name'] . '.' . $filter['table']['key'];

//what table to search and join tables

            foreach ($filter['select'] as $key => $select) {
                $sql .= ", " . $select . " as " . $key;
            }

            $sql .= " FROM " . DB_PREFIX . $filter['table']['full_name'] . " " . $filter['table']['name'] . " ";
            if (!empty($filter['tables'])) {
                foreach ($settings['extension'][$search]['query'] as $lul => $value) {
                    if ($value == 1) {
                        $azaza  = $filter['query'][$lul]['key'];
                        $join[] = substr($azaza, 0, strpos($azaza, "."));

                    }
                }
                foreach ($filter['tables'] as $table) {
                    if (in_array($table['name'], $join) || in_array($table['join_to'], $join)) {
                        $sql .= $table['join'] . " " . DB_PREFIX . $table['full_name'] . " " . $table['name'] . " ON ( " . $table['join_to'] . "." . $table['key'] . " = " . $table['name'] . "." . $table['key'] . " )";
                    }
                }
            }

            $sql .= " WHERE (";
            $implode = array();
            foreach ($filter['query'] as $key => $query) {
                if (isset($settings['extension'][$search]['query'][$key]) && $settings['extension'][$search]['query'][$key] == 0) {
                } else {

                    if ($query['rule'] == 'LIKE') {

                        if($query['key']=='pd.name' && $research){
                            foreach ($keywords as $key => $word) {
                                $implode[$search][] = "LOWER(".$query['key'] . ") LIKE LOWER('%" . $word . "%')";
                            }
                        }
                        $implode[$search][] = "LOWER(".$query['key'] . ") LIKE LOWER('%" . $text . "%')";

                    } else {
                        $implode[$search][] = $query['key'] . " " . $query['rule'] . " " . $text;
                    }
                }
            }
            if ($implode) {
                $sql .= " " . implode(" OR ", $implode[$search]) . ")";
            }

            if($search == 'product_simple' || $search == 'product'){
                // $sql .= " AND p.status = 1 AND pd.language_id = ". (int)$this->config->get('config_language_id');
                $sql .= " AND p.status = 1 AND pd.language_id = ". (int)$this->config->get('config_language_id') . " AND pt.store_id = " . (int)$this->config->get('config_store_id');
            }elseif ($search == 'category') {
                // $sql .= " AND c.status = 1 AND cd.language_id = ". (int)$this->config->get('config_language_id');
                $sql .= " AND c.status = 1 AND cd.language_id = ". (int)$this->config->get('config_language_id') . " AND ct.store_id = " . (int)$this->config->get('config_store_id');
            }




            $settings['extension'][$search]['max_count'] = isset($settings['extension'][$search]['max_count']) && !empty($settings['extension'][$search]['max_count']) ? $settings['extension'][$search]['max_count'] : 100;

            if (isset($settings['max_results']) && $settings['max_results'] != 0) {
                $sql .= " ORDER BY " . $filter['table']['name'] . '.' . $filter['table']['key'] . " DESC LIMIT " . $settings['extension'][$search]['max_count'] . "";
            }

            if ($search == 'blog') {
                $search = 'post';
            } elseif ($search == 'product_simple') {
                $search = 'product';
            }

            $sql_array[$search] = $sql;
        }

        $result       = array();
        $product_ides = array();
        foreach ($searches as $kek => $search) {
            if ($search == 'blog') {
                $search = 'post';
            } elseif ($search == 'product_simple') {
                $search = 'product';
            }
            $query = $this->db->query($sql_array[$search]);
            foreach ($query->rows as $key => $row) {

                if ((isset($product_ides[$search]) && in_array($row[$search . '_id'], $product_ides[$search]['id'])) || (isset($product_ides[$search]) && !empty($row['image'])  && $settings['no_dublicate_images'] && in_array($row['image'], $product_ides[$search]['image']))  ) {
                } else {

                    if(isset($settings['smart']) && $settings['smart']){

                        $sql       = "SELECT qr.count FROM " . DB_PREFIX . "as_query q LEFT JOIN " . DB_PREFIX . "as_query_results qr ON (q.query_id = qr.query_id) WHERE q.text = '" . $text . "' AND qr.type = '" . $search . "' AND qr.type_id = " . $row[$search . '_id'] . " AND qr.status = " . 1 . "  ORDER BY qr.count DESC";
                        $ai_result = $this->db->query($sql);
                    }

                    $product_ides[$search]['id'][]                = $row[$search . '_id'];
                    $product_ides[$search]['image'][]             = isset($row['image']) && !empty($row['image']) ? $this->model_tool_image->resize($row['image'], $settings['image_width'], $settings['image_height']) : $this->model_tool_image->resize('catalog/d_ajax_search/no_image_search.png', $settings['image_width'], $settings['image_height']);;
                    $result[$search][$key][$search . '_id'] = $row[$search . '_id'];
                    $result[$search][$key]['keyword'] = $text;
                    $result[$search][$key]['redirect'] = isset($redirect_text) ? $redirect_text : '';
                    $result[$search][$key]['autocomplite'] = isset($autocomplite) ? $autocomplite : '';
                    $result[$search][$key]['image']         = isset($row['image']) && !empty($row['image']) ? $this->model_tool_image->resize($row['image'], $settings['image_width'], $settings['image_height']) : $this->model_tool_image->resize('catalog/d_ajax_search/no_image_search.png', $settings['image_width'], $settings['image_height']);
                    $result[$search][$key]['name']          = $row['name'];
                    $result[$search][$key]['description']   = isset($row['description']) ? $row['description'] : '';
                    $result[$search][$key]['where_find']    = $this->language->get($search);
                    $result[$search][$key]['saggestion']    = isset($saggestion) ? $saggestion : '';
                    $result[$search][$key]['weight']        = isset($ai_result->rows[0]['count']) ? $ai_result->rows[0]['count'] : '';
                    $result[$search][$key]['item_data']     = $search . '_id=' . $row[$search . '_id'];
                    if ($search == 'category') {
                        $result[$search][$key]['href'] = html_entity_decode($this->url->link('product/' . $search, 'path=' . $row[$search . '_id']));
                    } else if ($search == 'manufacturer') {
                        $result[$search][$key]['href'] = html_entity_decode($this->url->link('product/' . $search . '/info', $search . '_id=' . $row[$search . '_id']));
                    } else if ($search == 'post') {
                        $result[$search][$key]['href'] = html_entity_decode($this->url->link('extension/d_blog_module/post', $search . '_id=' . $row[$search . '_id']));
                    } else {
                        $result[$search][$key]['href'] = html_entity_decode($this->url->link($search . '/' . $search, $search . '_id=' . $row[$search . '_id']));
                    }
                    if ($settings['price'] == 0) {
                        $result[$search][$key]['price'] = 0;
                    } else {
                        if(isset($row['price'])){
                            $result[$search][$key]['price'] = $this->currency->format($row['price'] , $this->session->data['currency']);
                        }else{
                            $result[$search][$key]['price']='';
                        }

                    }
                    if ($search == 'product') {
                        $info = $this->model_catalog_product->getProduct($row[$search . '_id']);
                    } elseif ($search == 'category') {
                        $info = $this->model_catalog_category->getCategory($row[$search . '_id']);
                    } elseif ($search == 'manufacturer') {
                        $info = $this->model_catalog_manufacturer->getManufacturer($row[$search . '_id']);
                    } elseif ($search == 'information') {
                        $info = $this->model_catalog_information->getInformation($row[$search . '_id']);
                    }
                    if (isset($info) && is_array($info)) {
                        foreach ($info as $gde => $string) {
                            $check = stripos($string, $text);
                            if ($check === false) {
                                $result[$search][$key]['find_by']=$this->language->get('name');
                            } else {
                                $result[$search][$key]['find_by'] = $this->language->get($gde);
                                break;
                            }
                        }
                    }
                }
            }
        }

        if($research==0){
            if(empty($result)){
                $result=$this->search($text, $searches, $research=1);
                return $result;
            }
        }

        $resultOut = array();
        foreach ($result as $val) {
            if (is_array($val)) {
                $resultOut = array_merge($resultOut, $val);
            }
        }
        array_splice($resultOut, $settings['max_results']);
        return $resultOut;
    }

    public function autocomplite($keyword=''){
        $setting1 = $this->model_setting_setting->getSetting('d_ajax_search');
        $settings = $setting1['d_ajax_search_setting'];
        $settings['autocomplete'] = isset($settings['autocomplete']) ? $settings['autocomplete'] : 0;

        if($settings['autocomplete']){
            $autocomplite_flag=0;

            $sql_autocomplite="SELECT text FROM `" . DB_PREFIX . "as_query` ORDER BY count DESC LIMIT 100";
            $query=$this->db->query($sql_autocomplite);
            $autocomplite='0';
            foreach ($query->rows as $key => $row) {

                similar_text( $keyword , $row['text'], $percent);

                if($percent > $autocomplite_flag && $percent > 65){
                    $autocomplite=$row['text'];
                    $autocomplite_flag=$percent;
                }
            }
            if($keyword !='' && strpos($autocomplite,$keyword) !== false){
                

            }else{
                $autocomplite='';
            }
            return $autocomplite;

        }
    }

    public function save_statistic($value) {
        
        $sql = "INSERT INTO `" . DB_PREFIX . "as_query`
        (`text`, `redirect`, `count`, `date_modify`)
        VALUES(
            '" . $this->db->escape($value['search']) . "',
            '" . '' . "',
            '" . 1 . "',
            NOW())
            ON DUPLICATE KEY UPDATE
            `count` = `count`+1,
            `date_modify` = NOW()";

            $this->db->query($sql);
            $last_id = $this->db->getLastId();

            $sql = "INSERT INTO `" . DB_PREFIX . "as_query_results`
            (`query_id`, `type`, `type_id`, `count`, `status`, `date_modify`)
            VALUES(
                '" . $this->db->escape($last_id) . "',
                '" . $this->db->escape($value['type']) . "',
                '" . $this->db->escape($value['type_id']) . "',
                '" . 1 . "',
                '" . 1 . "',
                NOW())
                ON DUPLICATE KEY UPDATE
                `count` = `count`+1,
                `date_modify` = NOW()";
                $this->db->query($sql);

                if ($this->customer->getId()) {
                    $sql = "INSERT INTO `" . DB_PREFIX . "as_customer_query`
                    (`customer_id`, `text`, `choose`, `type`, `type_id`, `count`, `date_modify`)
                    VALUES(
                        '" . $this->customer->getId() . "',
                        '" . $this->db->escape($value['search']) . "',
                        '" . $this->db->escape($value['select']) . "',
                        '" . $this->db->escape($value['type']) . "',
                        '" . $this->db->escape($value['type_id']) . "',
                        '" . 1 . "',
                        NOW())
                        ON DUPLICATE KEY UPDATE
                        `count` = `count`+1,
                        `date_modify` = NOW()";
                        $this->db->query($sql);
                    }

                    $sql = "INSERT INTO `" . DB_PREFIX . "as_statistic`
                    (`search`, `select`, `type`, `type_id`, `count`, `date_modify`)
                    VALUES(
                        '" . $this->db->escape($value['search']) . "',
                        '" . $this->db->escape($value['select']) . "',
                        '" . $this->db->escape($value['type']) . "',
                        '" . $this->db->escape($value['type_id']) . "',
                        '" . 1 . "',
                        NOW())
                        ON DUPLICATE KEY UPDATE
                        `count` = `count`+1,
                        `date_modify` = NOW()";
                        $this->db->query($sql);
                    }
                }
