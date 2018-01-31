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
        $sql="SELECT * FROM `" . DB_PREFIX . "as_statistic`";
        $query=$this->db->query($sql);
        $products=array();
        foreach ($query->rows as $key => $row) {
            $products[] = $row['select'];
        }

        $products=(array_count_values($products));

        foreach ($products as $products_key => $value) {

            $data['labels'][] = $products_key;
            $data['datasets']['0']['data'][] = (int)$value;
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
}