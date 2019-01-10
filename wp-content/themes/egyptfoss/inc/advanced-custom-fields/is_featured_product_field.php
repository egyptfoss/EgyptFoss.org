<?php
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_product-group',
		'title' => 'Featuring Product',
		'fields' => array (
			array (
				'key' => 'field_56dc05c094e27',
				'label' => 'Is featured',
				'name' => 'is_featured',
				'type' => 'true_false',
				'message' => '',
        'instructions' => 'This will feature the current language product only',
				'default_value' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'product',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),'options' => array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 5,
	));
}
