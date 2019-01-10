<?php
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_partner',
		'title' => 'partner',
		'fields' => array (
			array (
        'key' => 'field_5aba25c193518',
        'label' => 'link',
        'name' => 'link',
        'type' => 'text',
        'required' => 1,
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'formatting' => 'html',
        'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'partner',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
    'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
