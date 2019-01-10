<?php
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_news-fields',
		'title' => 'news fields',
		'fields' => array (
			array (
				'key' => 'field_56cc5fe4542c9',
				'label' => 'subtitle',
				'name' => 'subtitle',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_56cc61ce8efe3',
				'label' => 'description',
				'name' => 'description',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),array (
				'key' => 'field_57386b140aa9a',
				'label' => 'Category',
				'name' => 'news_category',
				'type' => 'taxonomy',
				'taxonomy' => 'news_category',
				'field_type' => 'select',
				'allow_null' => 1,
				'load_save_terms' => 0,
				'return_format' => 'id',
				'multiple' => 0,
			),
                        array (
				'key' => 'field_57386c687a790',
				'label' => 'Interest',
				'name' => 'interest',
				'type' => 'taxonomy',
				'instructions' => 'Select news interest. You can select more than one by holding Ctrl button',
				'taxonomy' => 'interest',
				'field_type' => 'multi_select',
				'allow_null' => 0,
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
					'value' => 'news',
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
