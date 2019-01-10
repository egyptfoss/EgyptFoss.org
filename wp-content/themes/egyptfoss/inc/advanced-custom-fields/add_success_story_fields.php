<?php

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_success_story-fields',
		'title' => 'success_story-fields',
		'fields' => array (
                        array (
				'key' => 'field_571789b286c69',
				'label' => 'Category',
				'name' => 'success_story_category',
				'type' => 'taxonomy',
				'taxonomy' => 'success_story_category',
				'field_type' => 'select',
				'allow_null' => 1,
				'load_save_terms' => 0,
				'return_format' => 'id',
				'multiple' => 0,
			),
			array (
				'key' => 'field_57012ea8be613',
				'label' => 'Interest',
				'name' => 'interest',
				'type' => 'taxonomy',
				'instructions' => 'Select success story interest. You can select more than one by holding Ctrl button',
				'taxonomy' => 'interest',
				'field_type' => 'multi_select',
				'allow_null' => 1,
				'load_save_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
			)
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'success_story',
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
