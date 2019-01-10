<?php
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_quiz_category',
		'title' => 'quiz_category',
		'fields' => array (
			array (
				'key' => 'field_57a73dcf63305',
				'label' => 'Category',
				'name' => 'category',
				'type' => 'taxonomy',
				'required' => 1,
				'taxonomy' => 'quiz_categories',
				'field_type' => 'select',
				'allow_null' => 0,
				'load_save_terms' => 0,
				'return_format' => 'id',
				'multiple' => 0,
			),
			array (
				'key' => 'field_57b2f50a50717',
				'label' => 'interest',
				'name' => 'interest',
				'type' => 'taxonomy',
				'taxonomy' => 'interest',
				'field_type' => 'multi_select',
				'allow_null' => 1,
				'load_save_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
			),        
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'quiz',
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
		),
		'menu_order' => 0,
	));
}
