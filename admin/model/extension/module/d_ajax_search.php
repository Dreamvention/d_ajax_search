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

    public function getStatistic(){
        $sql="SELECT * FROM `" . DB_PREFIX . "as_statistic` ORDER BY count DESC LIMIT 15";
        $query=$this->db->query($sql);
        // echo "<pre>"; print_r($query->rows); echo "</pre>";
        $products=array();
        foreach ($query->rows as $key => $row) {
            $products[] = $row;
        }
        // echo "<pre>"; print_r($products); echo "</pre>";
        // $products=(array_count_values($products));

        foreach ($products as $products_key => $value) {

            $data['labels'][] = $value['select'];
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
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgba(4, 92, 234, 0.5)',
            'rgba(234, 255, 10, 0.5)',
            'rgba(7, 232, 244, 0.5)',
            'rgba(244, 7, 221, 0.5)'
        ];
        $data['datasets']['0']['borderColor'] = [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(4, 92, 234, 1)',
            'rgba(234, 255, 10, 1)',
            'rgba(7, 232, 244, 1)',
            'rgba(244, 7, 221, 1)'
        ];
        return $data;
    }

    public function getRules(){

    }

    public function setRules(){

    }

    public function createDatabase(){


         $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "as_query` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `text` char(128) NOT NULL,
        `redirect` char(128) NOT NULL,
        `count` int NOT NULL,
        `date_modify` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `no_duplicate` (`text`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=MyISAM;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "as_query_results` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `query_id` int(11) NOT NULL,
        `type` char(128) NOT NULL,
        `type_id` char(128) NOT NULL,
        `count` int NOT NULL,
        `status` int NOT NULL,
        `date_modify` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `no_duplicate` (`type`,`type_id`)
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
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `search` char(128) NOT NULL,
        `select` char(128) NOT NULL,
        `count` int(11) NOT NULL,
         PRIMARY KEY (`id`),
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