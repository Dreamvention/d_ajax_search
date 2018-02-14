<?php

$_['heading_title'] = '<span style="color:#449DD0; font-weight:bold">Live Ajax Search</span><span style="font-size:12px; color:#999"> by <a href="http://www.opencart.com/index.php?route=extension/extension&filter_username=Dreamvention" style="font-size:1em; color:#999" target="_blank">Dreamvention</a></span>';
$_['heading_title_main']            = 'Live Ajax Search';

$_['entry_status']              ='Status';
$_['status_on']                ='On';
$_['status_off']                 ='Off';
$_['more_details']               ='More details';


$_['success_twig_compatible']       = 'Twig support successfully installed.';

// Text
//
$_['text_success']                     = 'Edit Live Ajax Search settings';
$_['text_edit']                     = 'Edit Live Ajax Search settings';
$_['text_modules']                  = 'Modules';
$_['text_settings']                 = 'Settings';
$_['text_instructions']             = 'Instructions';
$_['text_product']                  = 'Product';
$_['text_category']                 = 'Category';
$_['text_information']              = 'Information';
$_['text_manufacturer']             = 'Manufacture';
$_['text_blog_article']             = 'Blog Article';
$_['text_blog_category']            = 'Blog Category';
$_['text_success_af']                  = 'Success: You have modified module Live Ajax Search!';
$_['text_module']                   = 'Module';
$_['text_yes']                      = 'Yes';
$_['text_no']                       = 'No';
$_['text_enabled']                  = 'Enabled';
$_['text_disabled']                 = 'Disabled';
$_['text_success_no_update']        = 'Super! You have the latest version.';
$_['text_warning_new_update']       = 'Wow! There is a new version available for download.';
$_['text_error_update']             = 'Sorry! Something went wrong. If this repeats, contact the support please.';
$_['text_error_failed']             = 'Oops! We could not connect to the server. Please try again later.';
$_['text_instructions_full']        = '
<div class="col-sm-6">
<h2>How to use Ajax Search?</h2>
<p>Turn on module.</p>
<p>Set the sizes of the drop down in pixels or as a percentage.</p>
<h3>Set search results</h3>
<p>You can mark "Max Simbols" and set the max quantity symbols in the title and mark "Max Numbers" and set the max number items that user could see in the result list ( if 0 - use default value(5) ).</p>
<img src="view/image/d_ajax_search/d_ajax_search_result_dropdown.png" class="img-responsive"/>
<p>Once the customer enters the search field, the system will autocomplete his entry and offer a number of products with their name, image and price. Also, you could set this additional options: Search only first symbols in the title, Priority search, Display product price or special or both of them with/without tax. Search can return the products, categories, manufactures</p>
</div>
<div class="col-sm-6">
<h3>Set selector (class, name or id) of search input field</h3>
<p>If you are using a custom theme, and the Live Ajax search is not working, most likely you have another class selected for the search input field. just look into the code and set the correct class like - .oc_search, or #search_field etc.</p>
<img src="view/image/d_ajax_search/d_ajax_search_code_source.png" class="img-responsive"/>
<h2>How to get additional information about the module?</h2>
<p>You can write a letter of support in case of problems with the product.</p>
<span>Create a support ticket for more help:</span> <a href="http://www.dreamvention.com/support/" target="_blank" class="button">Create ticket</a>
</div>
';
// Entry

$_['entry_get_update']              = 'You have version %s';
$_['tooltip_smart_search']              = 'Ajax Search will learn your visitors queries and selected results and optimize the search results to increase click through rate.';
$_['tooltip_suggestion']              = 'In history and suggestions you can set for any query a suggestion. The search will use this to search for both the keyword and the suggestion. For example if there is a keyword with an error “ifone” you can add a suggestion “iPhone”.';
$_['tooltip_autocomplete']              = 'Allow the search field to help you visitors autocomplete their queries while they type. This will help them avoid mistakes and return better results.';
$_['entry_status']                  = 'Status';
$_['entry_on_off']                  = 'Enable';
$_['entry_width']                   = 'Width of dropdown';
$_['entry_max_symbols']             = 'Min Symbols';
$_['entry_max_results']             = 'Max Number of results';
$_['entry_first_symbols']           = 'Search only for first symbols in the title';
$_['entry_priority']                = 'Priority search';
$_['entry_class']                   = 'Set selector of the search feild';
$_['entry_price']                   = 'Display product price';
$_['entry_special']                 = 'Display product special';
$_['entry_tax']                     = 'Display product price and special with tax';
$_['entry_model']                   = 'Display product model:';
$_['entry_extended']                = 'Extend default search result';

//Help
$_['help_width']                    = 'Set the width of the result dropdown form. i.e. 100px or 100%';
$_['help_max_symbols']              = 'Min symbols for start search (if 0 - cut disabled)';
$_['help_max_results']              = 'Max number items in the result list ( if 0 - use default value(5) )';
$_['help_on_off']                   = 'Turn Ajax Search on/off';
$_['help_class']                    = 'Use in case you have a custom theme. Some themes create their own class for the input field which breaks the ajax search. You can learn your input selector by looking at the source code of the search field for class=search (write .search) or name=search (write [name=search]) or id=search (write #search), separate by comma ';
$_['help_first_symbols']            = 'This will force the search to look only from the begining of the product name (or category). i.e. writing letter c will show CD player, but will not show iMac';
$_['help_general_version']          = 'When there is a new version available, you can download it from the location you have purchased the module.';

// Button
$_['button_save']                   = 'Save';
$_['button_save_and_stay']          = 'Save and Stay';
$_['button_cancel']                 = 'Cancel';
$_['button_get_update']             = 'Check for update';

// Error
$_['error_permission']              = 'Warning: You do not have permission to modify module Live Ajax Search!';

?>