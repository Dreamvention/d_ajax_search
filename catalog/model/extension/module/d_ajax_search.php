<?php
class ModelExtensionModuleDAjaxSearch extends Model {
    private $id = 'd_ajax_search';
    public function search($text, $searches = array()){
        $this->load->model('tool/image');
        $search_filter = array();
        $setting1 = $this->model_setting_setting->getSetting($this->id);

        $settings = $setting1['d_ajax_search_setting'];
        foreach($searches as $search){
            $this->load->config('d_ajax_search/'.$search);

            $search_filter[$search] = $this->config->get('d_ajax_search_'.$search);

        }
        $sql_array=array();
        $sql='';

        foreach($search_filter as $search => $filter){

            $sql = 'SELECT '. $filter['table']['name']. '.' . $filter['table']['key'] ;

            //what table to search and join tables

            foreach($filter['select'] as $key => $select){
                $sql .=  ", " .$select ." as ". $key ;
            }

            $sql .= " FROM " . DB_PREFIX . $filter['table']['full_name'] . " " . $filter['table']['name'] . " ";

            if(!empty($filter['tables'])){
                foreach($filter['tables'] as $table){
                    $sql .= $table['join'] . " " . DB_PREFIX . $table['full_name'] . " " . $table['name'] . " ON ( " . $table['join_to'] . "." . $table['key'] . " = " . $table['name'] . "." . $table['key']." )";
                }
            }

            $sql .= " WHERE ";
            $implode=array();
            foreach($filter['query'] as $key => $query){
                if(isset($settings['extension'][$search]['query'][$key]) && $settings['extension'][$search]['query'][$key]==0){
                }else{

                    if($query['rule']=='LIKE'){
                        $implode[$search][] = $query['key']. " LIKE '%" . $text . "%'";
                    }else{
                        $implode[$search][] = $query['key']. " ". $query['rule'] ." " .$text;
                    }
                }
            }
            if ($implode) {
                $sql .= " " . implode(" OR ", $implode[$search]) . "";
            }

            if(isset($settings['max_results']) && $settings['max_results'] != 0) {
                $sql .= " ORDER BY ". $filter['table']['name']. '.' . $filter['table']['key'] ." DESC LIMIT ".$settings['max_results']."";
            }

            if ($search=='blog') {
                $search='post';
            }elseif ($search = 'product_simple') {
                $search='product';
            }
            $sql_array[$search]=$sql;
        }

        $result=array();
        $product_ides=array();
        foreach($searches as $kek => $search){
            if ($search=='blog') {
                $search='post';
            }else if ($search = 'product_simple') {
                $search='product';
            }
            $query=$this->db->query($sql_array[$search]);
            foreach ($query->rows as $key => $row) {
                if(isset($product_ides[$search]) && in_array($row[$search.'_id'],$product_ides[$search])){
                }else{
                    $product_ides[$search][]=$row[$search.'_id'];
                    $result[$search][$key][$search.'_id'] = $row[$search.'_id'];
                    $result[$search][$key]['image'] = isset($row['image']) ? $this->model_tool_image->resize($row['image'], 40, 40) : '';
                    $result[$search][$key]['name'] = $row['name'];
                    $result[$search][$key]['description'] =  isset($row['description']) ? $row['description'] : '';
                    if($search=='category'){
                        $result[$search][$key]['href']=$this->url->link('product/'.$search, 'path=' . $row[$search.'_id']);
                    }else if($search=='manufacturer'){
                        $result[$search][$key]['href']=$this->url->link('product/'.$search.'/info', $search.'_id=' . $row[$search.'_id']);
                    }else if($search=='post'){
                     $result[$search][$key]['href']=$this->url->link('extension/d_blog_module/post', $search.'_id=' . $row[$search.'_id']);
                 }else{
                    $result[$search][$key]['href']=$this->url->link($search.'/'.$search, $search.'_id=' . $row[$search.'_id']);
                }
                if($settings['price'] == 0){
                    $result[$search][$key]['price'] = 0;
                }else{
                    $result[$search][$key]['price'] = isset($row['price']) ? number_format($row['price'], 2, '.', '') : '';
                }
            }
        }
    }
    $resultOut = array();
    foreach($result as $val){
        if(is_array($val)){
          $resultOut = array_merge($resultOut,$val);
      }
  }
  return $resultOut;
}
}
