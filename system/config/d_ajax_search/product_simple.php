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
        ),
        array(
            'name' => 'pt',
            'join_to' => 'pd',
            'full_name' => 'product_to_store',
            'key' => 'product_id',
            'join' => 'LEFT JOIN',
            'multi_language' => 1
             )
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
        'price' => 'p.price',
        'store_id' => 'pt.store_id',
    )
);