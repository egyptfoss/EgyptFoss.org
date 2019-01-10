<?php
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_feature_news',
		'title' => 'News Feature',
		'fields' => array (
			array (
				'key' => 'field_56f12785b1293',
				'label' => 'Is featured',
				'name' => 'is_featured',
				'type' => 'true_false',
                                'instructions' => 'This will feature the current language news only',
				'message' => '',
				'default_value' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'news',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
