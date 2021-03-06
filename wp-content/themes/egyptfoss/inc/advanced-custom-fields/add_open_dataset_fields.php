<?php
if(function_exists("register_field_group")) {
	register_field_group(array (
		'id' => 'acf_open-dataset',
		'title' => 'open dataset',
		'fields' => array (
			array (
				'key' => 'field_5712435816807',
				'label' => 'Publisher',
				'name' => 'publisher',
				'type' => 'text',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5712439d16808',
				'label' => 'Description',
				'name' => 'description',
				'type' => 'textarea',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_571243db16809',
				'label' => 'Type',
				'name' => 'dataset_type',
				'type' => 'taxonomy',
				'required' => 1,
				'taxonomy' => 'dataset_type',
				'field_type' => 'select',
				'allow_null' => 1,
				'load_save_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
			),
			array (
				'key' => 'field_5712442e1680a',
				'label' => 'Theme',
				'name' => 'theme',
				'type' => 'taxonomy',
				'required' => 1,
				'taxonomy' => 'theme',
				'field_type' => 'select',
				'allow_null' => 1,
				'load_save_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
			),
			array (
				'key' => 'field_5712444e1680b',
				'label' => 'License',
				'name' => 'datasets_license',
				'type' => 'taxonomy',
				'taxonomy' => 'datasets_license',
				'field_type' => 'select',
				'allow_null' => 1,
				'load_save_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
			),
			array (
				'key' => 'field_571244721680c',
				'label' => 'Usage hints',
				'name' => 'usage_hints',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_5712447f1680d',
				'label' => 'References',
				'name' => 'references',
				'type' => 'textarea',
				'required' => 1,
				'instructions' => 'Enter multiple URLs each on a single line',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_571244f11680e',
				'label' => 'Resources',
				'name' => 'resources',
				'type' => 'repeater',
				'required' => 0,
				'sub_fields' => array (
					array (
						'key' => 'field_5713620dc5d37',
						'label' => 'Upload',
						'name' => 'upload',
						'type' => 'file',
						'instructions' => 'upload all dataset files',
						'required' => 1,
						'column_width' => '',
						'save_format' => 'object',
						'library' => 'all',
					),
          array (
						'key' => 'field_575d1a79e4b65',
						'label' => 'resource status',
						'name' => 'resource_status',
						'type' => 'select',
						'instructions' => 'set resource status to publish to show it to front users',
						'required' => 1,
						'column_width' => '',
						'choices' => array (
							'pending' => 'pending',
							'publish' => 'publish',
						),
						'default_value' => 'publish',
						'allow_null' => 0,
						'multiple' => 0,
					),
				),
				'row_min' => '',
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => 'Add Row',
			),
			array (
				'key' => 'field_571245401680f',
				'label' => 'Link to source',
				'name' => 'source_link',
				'type' => 'text',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5712456316810',
				'label' => 'Related interests',
				'name' => 'interest',
				'type' => 'taxonomy',
				'taxonomy' => 'interest',
				'field_type' => 'multi_select',
				'allow_null' => 1,
				'load_save_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
			),
      array (
				'key' => 'field_57286972e92e5',
				'label' => 'Published Date',
				'name' => 'published_date',
				'type' => 'date_picker',
				'date_format' => 'yy-mm-dd',
				'display_format' => 'yy-mm-dd',
				'first_day' => 1,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'open_dataset',
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
