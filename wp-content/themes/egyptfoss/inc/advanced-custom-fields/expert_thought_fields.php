<?php

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_expert-thought-custom-fields',
		'title' => 'expert-thought-custom-fields',
		'fields' => array (
			array (
				'key' => 'field_579772073343a',
				'label' => 'Related Interests',
				'name' => 'interest',
				'type' => 'taxonomy',
				'taxonomy' => 'interest',
				'field_type' => 'multi_select',
				'allow_null' => 1,
				'load_save_terms' => 0,
				'return_format' => 'id',
				'multiple' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'expert_thought',
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
