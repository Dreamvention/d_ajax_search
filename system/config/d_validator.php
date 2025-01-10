<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 22.05.2019
 * Time: 18:41
 */

$_['d_validator_setting'] = array(
    'config' => 'd_validator',

    'api_url' => 'https://api.shopunity.net/v1/external/validateLicence',
    'api_url_shopunity' => 'https://api.shopunity.net/v1/external/validateLicenceShopunity'
);

$_['d_validator_packs'] = array(
    '1' => '_pack',
    '2' => '_seo',
    '3' => '_pro',
);

$_['d_validator_modules'] = array(
    '1' => array
    (
        'trigger' => 'admin/view/extension/module/d_quick_order/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_quick_order',
        'json' => 'd_quick_order.json',
        'codename' => 'd_quick_order'
    ),
    '2' => array
    (
        'trigger' => 'admin/view/extension/d_ajax_filter/layout/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_ajax_filter/layout',
        'json' => 'd_ajax_filter_seo.json',
        'codename' => 'd_ajax_filter_seo'
    ),
    '3' => array
    (
        'trigger' => 'admin/view/extension/module/d_quickcheckout/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_quickcheckout',
        'json' => 'd_quickcheckout_pack.json',
        'codename' => 'd_quickcheckout_pack'
    ),
    '4' => array
    (
        'trigger' => 'admin/view/extension/module/d_blog_module/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_blog_module',
        'json' => 'd_blog_module_pack.json',
        'codename' => 'd_blog_module_pack'
    ),
    '5' => array
    (
        'trigger' => 'admin/view/extension/d_blog_module/post_list/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_blog_module/post_list',
        'json' => 'd_blog_module_pack.json',
        'codename' => 'd_blog_module_pack'
    ),
    '6' => array
    (
        'trigger' => 'admin/view/extension/d_blog_module/category_list/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_blog_module/category_list',
        'json' => 'd_blog_module_pack.json',
        'codename' => 'd_blog_module_pack'
    ),
    '7' => array
    (
        'trigger' => 'admin/view/extension/d_visual_designer/setting/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_visual_designer/setting',
        'json' => 'd_visual_designer_pack.json',
        'codename' => 'd_visual_designer_pack'
    ),
    '8' => array
    (
        'trigger' => 'admin/view/extension/module/d_visual_designer_module/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_visual_designer_module',
        'json' =>
            array(
                'd_visual_designer_pack.json',
                'd_visual_designer_module.json'
            ),
        'codename' =>
            array(
                'd_visual_designer_pack',
                'd_visual_designer_module'
            )
    ),
    '9' => array
    (
        'trigger' => 'admin/view/extension/d_visual_designer/designer/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_visual_designer/designer',
        'json' => 'd_visual_designer_pack.json',
        'codename' => 'd_visual_designer_pack'
    ),
    '10' => array
    (
        'trigger' => 'admin/view/extension/d_visual_designer_landing/page_list/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_visual_designer_landing/page_list',
        'json' =>
            array(
                'd_visual_designer_pack.json',
                'd_visual_designer_landing.json'
            ),
        'codename' =>
            array(
                'd_visual_designer_pack',
                'd_visual_designer_landing'
            )
    ),
//    '12' => array
//    (
//        'trigger' => 'admin/view/extension/d_visual_designer_landing/page_list/after',
//        'action' => 'extension/d_shopunity/d_validator/view',
//        'route' => 'extension/d_visual_designer_landing/page_list',
//        'json' => 'd_visual_designer_landing.json',
//        'codename' => 'd_visual_designer_landing'
//    ),
    '11' => array
    (
        'trigger' => 'admin/view/extension/module/d_download_manager_pro/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_download_manager_pro',
        'json' => 'd_download_manager_pro.json',
        'codename' => 'd_download_manager_pro'
    ),
    '12' => array
    (
        'trigger' => 'admin/view/extension/module/d_seo_module_manager/setting',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_seo_module_manager/setting',
        'json' => 'd_seo_module_pack.json',
        'codename' => 'd_seo_module_pack'
    ),
    '13' => array
    (
        'trigger' => 'admin/view/extension/module/d_seo_module_url/setting',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_seo_module_url/setting',
        'json' => 'd_seo_module_pack.json',
        'codename' => 'd_seo_module_pack'
    ),
    '14' => array
    (
        'trigger' => 'admin/view/extension/module/d_seo_module_blog/setting',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_seo_module_blog/setting',
        'json' => 'd_blog_module_pack.json',
        'codename' => 'd_blog_module_pack'
    ),
    '15' => array
    (
        'trigger' => 'admin/view/extension/module/d_seo_module/dashboard',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_seo_module/dashboard',
        'json' => 'd_seo_module_pack.json',
        'codename' => 'd_seo_module_pack'
    ),
    '16' => array
    (
        'trigger' => 'admin/view/extension/module/d_social_login/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_social_login',
        'json' => 'd_social_login_pro.json',
        'codename' => 'd_social_login_pro'
    ),
    '17' => array
    (
        'trigger' => 'admin/view/extension/d_export_import/excel/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_export_import/excel',
        'json' => 'd_export_import_pro.json',
        'codename' => 'd_export_import_pro'
    ),
    '18' => array
    (
        'trigger' => 'admin/view/extension/d_gdpr_module/d_gdpr_module',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_gdpr_module/d_gdpr_module',
        'json' => 'd_gdpr_module.json',
        'codename' => 'd_gdpr_module'
    ),
    '19' => array
    (
        'trigger' => 'admin/view/extension/d_security_module/d_security_module/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/d_security_module/d_security_module',
        'json' => 'd_security_module_pro.json',
        'codename' => 'd_security_module_pro'
    ),
    '20' => array
    (
        'trigger' => 'admin/view/extension/module/d_social_like/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_social_like',
        'json' => 'd_social_like_pro.json',
        'codename' => 'd_social_like_pro'
    ),
    '21' => array
    (
        'trigger' => 'admin/view/extension/feed/d_google_sitemap/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/feed/d_google_sitemap',
        'json' => 'd_google_sitemap.json',
        'codename' => 'd_google_sitemap'
    ),

    '22' => array
    (
        'trigger' => 'admin/view/extension/module/d_cart_related/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_cart_related',
        'json' => 'd_cart_related.json',
        'codename' => 'd_cart_related'
    ),
    '23' => array
    (
        'trigger' => 'admin/view/extension/module/d_gift_with_purchase/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_gift_with_purchase',
        'json' => 'd_gift_with_purchase.json',
        'codename' => 'd_gift_with_purchase'
    ),
    '24' => array
    (
        'trigger' => 'admin/view/extension/shipping/d_multiflat/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/shipping/d_multiflat',
        'json' => 'd_multiflat.json',
        'codename' => 'd_multiflat'
    ),
    '25' => array
    (
        'trigger' => 'admin/view/extension/module/d_image_product_tag/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_image_product_tag',
        'json' => 'd_image_product_tag.json',
        'codename' => 'd_image_product_tag'
    ),
    '26' => array
    (
        'trigger' => 'admin/view/extension/module/d_shipping_based_payment/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_shipping_based_payment',
        'json' => 'd_shipping_based_payment.json',
        'codename' => 'd_shipping_based_payment'
    ),
    '27' => array
    (
        'trigger' => 'admin/view/extension/module/d_payment_by_customer_group/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_payment_by_customer_group',
        'json' => 'd_payment_by_customer_group.json',
        'codename' => 'd_payment_by_customer_group'
    ),
    '28' => array
    (
        'trigger' => 'admin/view/extension/module/d_shipping_by_customer_group/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_shipping_by_customer_group',
        'json' => 'd_shipping_by_customer_group.json',
        'codename' => 'd_shipping_by_customer_group'
    ),
    '29' => array
    (
        'trigger' => 'admin/view/extension/total/d_payment_fee/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/total/d_payment_fee',
        'json' => 'd_payment_fee.json',
        'codename' => 'd_payment_fee'
    ),
    '30' => array
    (
        'trigger' => 'admin/view/extension/module/d_link_cart/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_link_cart',
        'json' => 'd_link_cart.json',
        'codename' => 'd_link_cart'
    ),
    '31' => array
    (
        'trigger' => 'admin/view/extension/module/d_seo_module_adviser/setting/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_seo_module_adviser/setting',
        'json' => 'd_seo_module_pack.json',
        'codename' => 'd_seo_module_pack'
    ),
    '32' => array
    (
        'trigger' => 'admin/view/extension/module/d_contact_map/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_contact_map',
        'json' => 'd_contact_map.json',
        'codename' => 'd_contact_map'
    ),
    '33' => array
    (
        'trigger' => 'admin/view/extension/shipping/d_free_shipping_advanced/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/shipping/d_free_shipping_advanced',
        'json' => 'd_free_shipping_advanced.json',
        'codename' => 'd_free_shipping_advanced'
    ),
    '34' => array
    (
        'trigger' => 'admin/view/extension/module/d_ajax_search/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_ajax_search',
        'json' => 'd_ajax_search_pro.json',
        'codename' => 'd_ajax_search_pro'
    ),
    '35' => array
    (
        'trigger' => 'admin/view/extension/module/d_spin_product/after',
        'action' => 'extension/d_shopunity/d_validator/view',
        'route' => 'extension/module/d_spin_product',
        'json' => 'd_spin_product.json',
        'codename' => 'd_spin_product'
    ),
);
