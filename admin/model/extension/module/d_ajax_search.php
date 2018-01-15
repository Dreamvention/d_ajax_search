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
}