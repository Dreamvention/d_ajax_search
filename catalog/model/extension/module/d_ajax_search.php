<?php
class ModelExtensionModuleDAjaxSearch extends Model {
    private $id = 'd_ajax_search';
    public function search($text, $searches = array(), $research=0) {
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

        if($research){
            $sql_smart="SELECT * FROM `" . DB_PREFIX . "as_query` ORDER BY count DESC LIMIT 15";
            $query=$this->db->query($sql_smart);
            $gml=0;
            $new_text='';
            foreach ($query->rows as $key => $row) {
                similar_text( $text , $row['text'], $percent);

                if($percent > $gml && $percent > 65){
                    $new_text=$row['text'];
                    $gml=$percent;
                }
            }

            if(!empty($new_text)){
                $text=$new_text;
            }
            FB::log('new text '.$new_text);
            FB::log('search text '.$text);
        }


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

            $sql .= " WHERE ";
            $implode = array();
            foreach ($filter['query'] as $key => $query) {
                if (isset($settings['extension'][$search]['query'][$key]) && $settings['extension'][$search]['query'][$key] == 0) {
                } else {

                    if ($query['rule'] == 'LIKE') {
                        $implode[$search][] = $query['key'] . " LIKE '%" . $text . "%'";
                    } else {
                        $implode[$search][] = $query['key'] . " " . $query['rule'] . " " . $text;
                    }
                }
            }
            if ($implode) {
                $sql .= " " . implode(" OR ", $implode[$search]) . "";
            }

            if (isset($settings['max_results']) && $settings['max_results'] != 0) {
                $sql .= " ORDER BY " . $filter['table']['name'] . '.' . $filter['table']['key'] . " DESC LIMIT " . $settings['max_results'] . "";
            }

            if ($search == 'blog') {
                $search = 'post';
            } elseif ($search == 'product_simple') {
                $search = 'product';
            }
            $sql_array[$search] = $sql;
        }
        // echo "<pre>"; print_r($sql_array); echo "</pre>";exit;
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
                if (isset($product_ides[$search]) && in_array($row[$search . '_id'], $product_ides[$search])) {
                } else {

                    $sql       = "SELECT qr.count FROM " . DB_PREFIX . "as_query q LEFT JOIN " . DB_PREFIX . "as_query_results qr ON (q.query_id = qr.query_id) WHERE q.text = '" . $text . "' AND qr.type = '" . $search . "' AND qr.type_id = " . $row[$search . '_id'] . " AND qr.status = " . 1 . "  ORDER BY qr.count DESC";
                    $ai_result = $this->db->query($sql);

                    $product_ides[$search][]                = $row[$search . '_id'];
                    $result[$search][$key][$search . '_id'] = $row[$search . '_id'];
                    $result[$search][$key]['image']         = isset($row['image']) ? $this->model_tool_image->resize($row['image'], $settings['image_width'], $settings['image_width']) : '';
                    $result[$search][$key]['name']          = $row['name'];
                    $result[$search][$key]['description']   = isset($row['description']) ? $row['description'] : '';
                    $result[$search][$key]['where_find']    = $search;
                    $result[$search][$key]['weight']        = isset($ai_result->rows[0]['count']) ? $ai_result->rows[0]['count'] : '';
                    if ($search == 'category') {
                        $result[$search][$key]['href'] = $this->url->link('product/' . $search, 'path=' . $row[$search . '_id']);
                    } else if ($search == 'manufacturer') {
                        $result[$search][$key]['href'] = $this->url->link('product/' . $search . '/info', $search . '_id=' . $row[$search . '_id']);
                    } else if ($search == 'post') {
                        $result[$search][$key]['href'] = $this->url->link('extension/d_blog_module/post', $search . '_id=' . $row[$search . '_id']);
                    } else {
                        $result[$search][$key]['href'] = $this->url->link($search . '/' . $search, $search . '_id=' . $row[$search . '_id']);
                    }
                    if ($settings['price'] == 0) {
                        $result[$search][$key]['price'] = 0;
                    } else {
                        $result[$search][$key]['price'] = isset($row['price']) ? number_format($row['price'], 2, '.', '') : '';
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
                    if (isset($info)) {
                        foreach ($info as $gde => $string) {
                            $check = stripos($string, $text);
                            if ($check === false) {
                            } else {
                                $result[$search][$key]['find_by'] = $gde;
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
        // echo "<pre>"; print_r($result); echo "</pre>";
        $resultOut = array();
        foreach ($result as $val) {
            if (is_array($val)) {
                $resultOut = array_merge($resultOut, $val);
            }
        }
        array_splice($resultOut, $settings['max_results']);
        return $resultOut;
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
        `date_modify` = 'NOW()'";

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
            `date_modify` = 'NOW()'";
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
                `date_modify` = 'NOW()'";
                FB::log($sql);
            $this->db->query($sql);
        }

        $sql = "INSERT INTO `" . DB_PREFIX . "as_statistic`
        (`search`, `select`, `count`)
        VALUES(
            '" . $this->db->escape($value['search']) . "',
            '" . $this->db->escape($value['select']) . "',
            '" . 1 . "')
            ON DUPLICATE KEY UPDATE
            `count` = `count`+1";
        $this->db->query($sql);
    }
}
