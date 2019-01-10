<?php
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_feedback',
		'title' => 'feedback',
		'fields' => array (
			array (
				'key' => 'field_57612555cce3b',
				'label' => 'Sections',
				'name' => 'sections',
				'type' => 'select',
				'choices' => array (
					'general' => 'General',
          'quiz' => 'Awareness Center',
          'collaboration-center' => 'Collaboration Center',
          'event' => 'Events',
          'fossmap' => 'FOSS Map',
          'fosspedia' => 'FOSSPedia',
          'open-dataset' => 'Open Datasets',    
          'news' => 'News',
          'product' => 'Products',
          'success-story' => 'Success Stories',
          'request-center' => 'Request Center'
				),
				'default_value' => '',
				'allow_null' => 1,
				'multiple' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'feedback',
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
