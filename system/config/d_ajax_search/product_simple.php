<?php
$_['d_ajax_search_product_simple'] = array(
    'table' => array(
        'name' => 'p',
        'full_name' => 'product',
        'key' => 'product_id'
    ),
    'tables' => array(
        array(
            'name' => 'pd',
            'join_to' => 'p',
            'full_name' => 'product_description',
            'key' => 'product_id',
            'join' => 'LEFT JOIN',
            'multi_language' => 1
             )
        // array(
        //     'name' => 'pc',
        //     'join_to' => 'p',
        //     'full_name' => 'product_to_category',
        //     'key' => 'product_id',
        //     'join' => 'LEFT JOIN',
        //     'multi_language' => 1

        // ),
        // array(
        //     'name' => 'pa',
        //     'join_to' => 'p',
        //     'full_name' => 'product_attribute',
        //     'key' => 'product_id',
        //     'join' => 'LEFT JOIN',
        //     'multi_language' => 1

        // ),
        // array(
        //     'name' => 'cd',
        //     'join_to' => 'pc',
        //     'full_name' => 'category_description',
        //     'key' => 'category_id',
        //     'join' => 'LEFT JOIN',
        //     'multi_language' => 1
        // ),
        // array(
        //     'name' => 'm',
        //     'join_to' => 'p',
        //     'full_name' => 'manufacturer',
        //     'key' => 'manufacturer_id',
        //     'join' => 'LEFT JOIN',
        //     'multi_language' => 1

        // ),
    ),
    'query' => array(

        'Name' => array(
            'key' => 'pd.name',
            'rule' => 'LIKE',
            'tooltip' => 'Search by Name'),

        'Description' => array(
            'key' => 'pd.description',
            'rule' => 'LIKE',
            'tooltip' => 'Search by Description'),

        'Model' => array(
            'key' => 'p.model',
            'rule' => 'LIKE',
            'tooltip' => 'Search by Model'),

        ),


    'select' => array(
        'image' => 'p.image',
        'name' => 'pd.name',
        'description' => 'pd.description',
        'price' => 'p.price'
    )
);