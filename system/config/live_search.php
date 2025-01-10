<?php
$_['live_search_setting'] = [
            'class' => '[name=search], [name=filter_name], [name=search_oc], #search_input',
            'class_form' => '#search',
            'width' => '35.0%',
            'max_symbols' => 0,
            'max_results' => 7,
            'price' => 1,
            'image_height' => 60,
            'image_width' => 60,
            'where_search' => 1,
            'block_result' => 1,
            'smart'=>0,
            'suggestion' => 0,
            'all_result_count' => 5,
            'all_result_status' => 1,
            'no_dublicate_images' => 0
];
$_['live_search_events'] = [
    0 => [
        'code' => 'live_search',
        'description' => '',
        'trigger' => 'admin/view/customer/customer_form/after',
        'action' => 'extension/live_search/module/live_search|view_customer_customer_form_after',
        'status' => 1,
        'sort_order' => 0
    ],
    1 => [
        'code' => 'live_search',
        'description' => '',
        'trigger' => 'catalog/controller/common/header/before',
        'action' => 'extension/live_search/module/live_search|controller_common_header_before',
        'status' => 1,
        'sort_order' => 0
    ],
    2 => [
        'code' => 'live_search',
        'description' => '',
        'trigger' => 'catalog/view/common/header/after',
        'action' => 'extension/live_search/module/live_search|view_common_header_after',
        'status' => 1,
        'sort_order' => 0
    ]
];