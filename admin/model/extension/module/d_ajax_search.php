<?php
/*
 * location: admin/model
 */
class ModelExtensionModuleDAjaxSearch extends Model
{
    public function getExtensions(){
        $files = glob(DIR_APPLICATION . 'controller/extension/d_ajax_search/*.php');
        $extensions = array();
        if ($files) {
            foreach ($files as $file) {
                $extensions[] = basename($file, '.php');
            }
        }
        return $extensions;
    }

     public function getTopsearches($day=1){
        $sql="SELECT * FROM `" . DB_PREFIX . "as_query` WHERE date_modify >= now() - INTERVAL ". $day ." DAY ORDER BY count DESC LIMIT 10";
        $query=$this->db->query($sql);
        $products=array();
        foreach ($query->rows as $key => $row) {
            $products[] = $row;
        }
        $data['table_searches']=$products;

        foreach ($products as $products_key => $value) {
            $data['labels'][] = $value['text'];
            $data['datasets']['0']['data'][] = (int)$value['count'];
            if(!empty($data['labels']) && count($data['labels']) == 10){
                break;
            }
        }
        if(empty($data['labels'])){
            $data['error'] = 'empty-chart';
        }
        $data['datasets']['0']['label'] = 'Top keywords';
        $data['datasets']['0']['borderWidth'] = 2;
        $data['datasets']['0']['backgroundColor'] = [
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)'
        ];
        $data['datasets']['0']['borderColor'] = [
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)'
        ];
        return $data;
    }

    public function getStatistic($day=1){

        $url_token='';
        if (isset($this->session->data['token'])) {
            $url_token .= 'token=' . $this->session->data['token'];
        }

        if (isset($this->session->data['user_token'])) {
            $url_token .= 'user_token=' . $this->session->data['user_token'];
        }


        $sql="SELECT * FROM `" . DB_PREFIX . "as_statistic` WHERE date_modify >= now() - INTERVAL ". $day ." DAY ORDER BY count DESC LIMIT 10";
        $query=$this->db->query($sql);
        $products=array();
        foreach ($query->rows as $key => $row) {
            $products[] = $row;
        }
        $results=array();
        $data['table_statistic']=array();

        foreach ($query->rows as $key => $value) {

            if ($value['type'] == 'product') {
                $data['table_statistic'][$key]['href']=$this->url->link('catalog/product/edit',  $url_token . '&product_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'path') {
                $value['type'] = 'category';
                $data['table_statistic'][$key]['href']=$this->url->link('catalog/category/edit',  $url_token . '&category_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'manufacturer') {
                $data['table_statistic'][$key]['href']=$this->url->link('catalog/manufacturer/edit',  $url_token . '&manufacturer_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'information') {
                $data['table_statistic'][$key]['href']=$this->url->link('catalog/information/edit',  $url_token . '&information_id=' . $value['type_id'], true);
            }
            $data['table_statistic'][$key]['type']=$value['type'];
            $data['table_statistic'][$key]['select']=$value['select'];
            $data['table_statistic'][$key]['count']=$value['count'];

}



        foreach ($products as $products_key => $value) {
            $data['labels'][] = $products_key+1;
            $data['datasets']['0']['data'][] = (int)$value['count'];
            if(!empty($data['labels']) && count($data['labels']) == 10){
                break;
            }
        }
        if(empty($data['labels'])){
            $data['error'] = 'empty-chart';
        }
        $data['datasets']['0']['label'] = 'Top Viewed';
        $data['datasets']['0']['borderWidth'] = 2;
        $data['datasets']['0']['backgroundColor'] = [
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(54, 162, 235, 0.5)'
        ];
        $data['datasets']['0']['borderColor'] = [
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 1)'
        ];
        return $data;
    }

    public function allHistory(){
         $sql= "SELECT * FROM " . DB_PREFIX . "as_query q LEFT JOIN " . DB_PREFIX . "as_query_results qr ON (q.query_id = qr.query_id) ORDER BY qr.count DESC";
         $all = $this->db->query($sql);
         return $all->rows;
    }

    public function getHistory($data=array()){
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/information');
        $this->load->model('tool/image');

        $sql= "SELECT * FROM " . DB_PREFIX . "as_query q LEFT JOIN " . DB_PREFIX . "as_query_results qr ON (q.query_id = qr.query_id)";

        if(isset($data['keyword']) && !empty($data['keyword'])){

            $sql .= " WHERE text = '" . $data['keyword'] . "'";
        }

        $sql.=" ORDER BY qr.count DESC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $url_token = '';

        if (isset($this->session->data['token'])) {
            $url_token .=  'token=' . $this->session->data['token'];
        }

        if (isset($this->session->data['user_token'])) {
            $url_token .= 'user_token=' . $this->session->data['user_token'];
        }


        $ai_results = $this->db->query($sql);
        $results=array();

        foreach ($ai_results->rows as $key => $value) {
            if ($value['type'] == 'product') {
                $info = $this->model_catalog_product->getProduct($value['type_id']);
                $results[$key]['href']=$this->url->link('catalog/product/edit',  $url_token . '&product_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'path') {
                $value['type'] == 'category';
                $info = $this->model_catalog_category->getCategory($value['type_id']);
                $results[$key]['href']=$this->url->link('catalog/category/edit',  $url_token . '&category_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'manufacturer') {
                $info = $this->model_catalog_manufacturer->getManufacturer($value['type_id']);
                $results[$key]['href']=$this->url->link('catalog/manufacturer/edit',  $url_token . '&manufacturer_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'information') {
                $info = $this->model_catalog_information->getInformation($value['type_id']);
                $description = $this->model_catalog_information->getInformationDescriptions($value['type_id']);
                //kek
                $info['name'] = $description[1]['title'];
                $results[$key]['href']=$this->url->link('catalog/information/edit',  $url_token . '&information_id=' . $value['type_id'], true);
            }
        if(isset($info)){
            $results[$key]['query_id']=$value['query_id'];
            $results[$key]['name']=$info['name'];
            $results[$key]['keyword']=$value['text'];
            $results[$key]['count']=$value['count'];
            $results[$key]['redirect']=$value['redirect'];
            $results[$key]['image']= isset($info['image']) && !empty($info['image']) ? $this->model_tool_image->resize($info['image'], 60, 60) : $this->model_tool_image->resize('catalog/d_ajax_search/no_image_search.png', 60, 60);
        }
}
        return $results;

    }

    public function getCustomerHistory($data=array()){
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/information');
        $this->load->model('tool/image');
        $sql= "SELECT * FROM " . DB_PREFIX . "as_customer_query WHERE customer_id = '" . (int)$data['customer_id'] . "'";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $customer_history = $this->db->query($sql);
        $results=array();
        foreach ($customer_history->rows as $key => $value) {
            if ($value['type'] == 'product') {
                $info = $this->model_catalog_product->getProduct($value['type_id']);
                $results[$key]['href']=$this->url->link('catalog/product/edit',  $data['url_token'] . '&product_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'path') {
                $value['type'] == 'category';
                $info = $this->model_catalog_category->getCategory($value['type_id']);
                $results[$key]['href']=$this->url->link('catalog/category/edit',  $data['url_token'] . '&category_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'manufacturer') {
                $info = $this->model_catalog_manufacturer->getManufacturer($value['type_id']);
                $results[$key]['href']=$this->url->link('catalog/manufacturer/edit',  $data['url_token'] . '&manufacturer_id=' . $value['type_id'], true);

            } elseif ($value['type'] == 'information') {
                $info = $this->model_catalog_information->getInformation($value['type_id']);
                $results[$key]['href']=$this->url->link('catalog/information/edit',  $data['url_token'] . '&information_id=' . $value['type_id'], true);
            }
        if(isset($info)){
            $results[$key]['name']=$info['name'];
            $results[$key]['keyword']=$value['text'];
            $results[$key]['count']=$value['count'];
            $results[$key]['image']= isset($info['image']) && !empty($info['image']) ? $this->model_tool_image->resize($info['image'], 60, 60) : $this->model_tool_image->resize('catalog/d_ajax_search/no_image_search.png', 60, 60);
        }
}
        return $results;

    }

    public function updateRedirect($query_id, $text){
        $this->db->query("UPDATE " . DB_PREFIX . "as_query SET redirect = '" . $this->db->escape($text) . "' WHERE query_id = '" . (int)$query_id . "'");
    }

    public function createDatabase(){


         $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "as_query` (
        `query_id` int(11) NOT NULL AUTO_INCREMENT,
        `text` char(128) NOT NULL,
        `redirect` char(128) NOT NULL,
        `count` int NOT NULL,
        `date_modify` datetime NOT NULL,
        PRIMARY KEY (`query_id`),
        UNIQUE KEY `no_duplicate` (`text`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=MyISAM;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "as_query_results` (
        `query_results_id` int(11) NOT NULL AUTO_INCREMENT,
        `query_id` int(11) NOT NULL,
        `type` char(128) NOT NULL,
        `type_id` char(128) NOT NULL,
        `count` int NOT NULL,
        `status` int NOT NULL,
        `date_modify` datetime NOT NULL,
        PRIMARY KEY (`query_results_id`),
        UNIQUE KEY `no_duplicate` (`query_id`,`type`,`type_id`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=MyISAM;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "as_customer_query` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `text` char(128) NOT NULL,
        `choose` char(128) NOT NULL,
        `type` char(128) NOT NULL,
        `type_id` char(128) NOT NULL,
        `count` int NOT NULL,
        `date_modify` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `no_duplicate` (`customer_id`, `text`, `choose`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=MyISAM;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "as_statistic` (
        `statistic_id` int(11) NOT NULL AUTO_INCREMENT,
        `search` char(128) NOT NULL,
        `select` char(128) NOT NULL,
        `type` char(128) NOT NULL,
        `type_id` char(128) NOT NULL,
        `count` int(11) NOT NULL,
        `date_modify` datetime NOT NULL,
         PRIMARY KEY (`statistic_id`),
        UNIQUE KEY `no_duplicate` (`select`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=MyISAM;");

    }

    public function dropDatabase(){

        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "as_query`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "as_query_results`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "as_customer_query`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "as_statistic`");
    }
}