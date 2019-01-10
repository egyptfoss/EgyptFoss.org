<?php

class testData
{
	public static function returnUsers()
	{
		$test_users = array(
			array(
				'username' => 'bougy.tamtam',
				'email'	=> 'bougy.tamtam10@gmail.com',
				'password'	=> '123456789',
				'enabled'	=> 'yes',
			),
			array(
				'username' => 'maii.test',
				'email'	=> 'maii.elnagar+maii.test@espace.com.eg',
				'password'	=> '123456789',
				'enabled'	=> 'yes',
			),
			array(
				'username' => 'nour.tarek',
				'email'	=> 'bougy.tamtam10+nour.tarek@gmail.com',
				'password'	=> '123456789',
				'enabled'	=> 'yes',
			),
			array(
				'username' => 'leen.tarek',
				'email'	=> 'bougy.tamtam10+leen.tarek@gmail.com',
				'password'	=> '123456789',
				'enabled'	=> 'no',
			),
			array(
				'username' => 'maii.amer',
				'email'	=> 'bougy.tamtam15+maii.amer@gmail.com',
				'password'	=> '123456789',
				'enabled'	=> 'no',
			),
			array(
				'username' => 'bougy.tamtam10',
				'email'	=> 'bougy.tamtam10@gmail.com',
				'password'	=> '123456789',
				'enabled'	=> 'yes',
			),
    array(
        'username' => 'espace',
        'email' => 'maii.elnagar@espace.com.eg',
        'password'  => '123456789',
        'enabled'   => 'yes',
      ),
      array(
				'username' => 'expert_user',
				'email'	=> 'expert_user@espace.com.eg',
				'password'	=> '123456789',
				'enabled'	=> 'yes',
        'expert' => 1
			),
      array(
        'username' => 'subscriber_user',
        'email' => 'sub@user.com',
        'password'  => '123456789',
        'enabled'   => 'yes',
        'role' =>  'a:1:{s:10:"subscriber";b:1;}'
			),	              
      array(
				'username' => 'pedia.contributor',
				'email'	=> 'fosspedia_contributor@espace.com.eg',
				'password'	=> '123456789',
				'enabled'	=> 'yes',
        'role' => 'contributor'
			),
      array(
				'username' => 'collaboration.contributor',
				'email'	=> 'collaboration_contributor@espace.com.eg',
				'password'	=> '123456789',
				'enabled'	=> 'yes',
        'role' => 'contributor'
			)	        
		);

		return $test_users;
	}
        
        
        public static function returnNews()
	{
            $test_news = array(
                array(
                    'post_title' => 'new-test-news-title-EGYPT-FOSS-Ar',
                    'post_name'	=> 'new-test-news-title-EGYPT-FOSS-Ar',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),                 
                array(
                    'post_title' => 'new-test-news-title-EGYPT-FOSS',
                    'post_name'	=> 'new-test-news-title-EGYPT-FOSS',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ), 
                array(
                    'post_title' => 'new-test-news-title-EGYPT-FOSS-55',
                    'post_name'	=> 'new-test-news-title-EGYPT-FOSS-55',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),                        
                array(
                    'post_title' => 'New News 1',
                    'post_name'	=> 'news-1',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 2',
                    'post_name'	=> 'news-2',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 3',
                    'post_name'	=> 'news-3',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 4',
                    'post_name'	=> 'news-4',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 5',
                    'post_name'	=> 'news-5',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 6',
                    'post_name'	=> 'news-6',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 7',
                    'post_name'	=> 'news-7',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 8',
                    'post_name'	=> 'news-8',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 9',
                    'post_name'	=> 'news-9',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 10',
                    'post_name'	=> 'news-10',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 11',
                    'post_name'	=> 'news-11',
                    'post_type'	=> 'news',
                    'post_status' => 'publish',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 12',
                    'post_name'	=> 'news-12',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 13',
                    'post_name'	=> 'news-13',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 14',
                    'post_name'	=> 'news-14',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 15',
                    'post_name'	=> 'news-15',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 16',
                    'post_name'	=> 'news-16',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 17',
                    'post_name'	=> 'news-17',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 18',
                    'post_name'	=> 'news-18',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 19',
                    'post_name'	=> 'news-19',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 20',
                    'post_name'	=> 'news-20',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                ),
                array(
                    'post_title' => 'New News 21',
                    'post_name'	=> 'news-21',
                    'post_type'	=> 'news',
                    'post_status' => 'pending',
                    'guid' => ''
                )               
            );
            
            return $test_news;
        }
        
    public static function returnVenues()
    {
        $test_venues = array(
            array(
                'venue' => 'Venue1',
                'lang'	=> 'en',
                'venue_address'	=> 'Alexandria, Egypt',
                'venue_latitude' => '31.2512731',
                'venue_longitude' => '29.9685768',
                'venue_city' => 'Alexandria',
                'venue_country' => 'Egypt',
                'venue_phone' => '0000000000000',
            )
        );
        
        return $test_venues;
    }
    
    public static function returnOrganizers()
    {
        $test_organizers = array(
            array(
                'organizer' => 'Organizer1',
                'lang'	=> 'en',
                'organizer_email' => 'test@test.com',
                'organizer_phone' => '0000000000000',
            )
        );
        
        return $test_organizers;
    }
    
    public static function returnEvents()
    {
        $test_events = array(
            array(
                'title' => 'new-test-event-title-egypt-foss',
                'description' => 'description 1',
                'lang'	=> 'en',
                'start_datetime' => date('Y-m-d H:m:s', strtotime("+2 days")),
                'end_datetime' => date('Y-m-d H:m:s', strtotime("+3 days")),
                'venue' => 'Venue1',
                'organizer' => 'Organizer1',
                'event_type' => 'Competitions',
                'event_website' => 'http://www.google.com',
                'audience' => 'audience',
                'objectives' => 'objectives',
                'functionality' => 'functionality',
                'prerequisites' => 'prerequisites',
                'cost' => '10',
                'currency' => 'EGP',
            )
        );
        
        return $test_events;
    }

    public static function returnTimeline()
    {
        $test_timeline = array(
            array(
                'user' => 'espace',
                'component' => 'activity',
                'type' => 'activity_update',
                'content' => 'Good Morning great team from Saudi Arabia!'
            ),
            array(
                'user' => 'espace',
                'component' => 'activity',
                'type' => 'activity_update',
                'content' => 'Good Morning great team from Saudi Arabia-1!'
            ),
            array(
                'user' => 'foss',
                'component' => 'activity',
                'type' => 'activity_update',
                'content' => 'Good Morning team!'
            ),
            array(
                'user' => 'foss',
                'component' => 'activity',
                'type' => 'activity_update',
                'content' => 'Good Morning foss'
            )            
        );
        
        return $test_timeline;
    }
    
    public static function returnSuccessStories()
    {
        $test_news = array(
            array(
                'post_title' => 'new-test-success-title-EGYPT-FOSS-Ar',
                'post_name'	=> 'new-test-success-title-EGYPT-FOSS-Ar',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),                 
            array(
                'post_title' => 'new-test-success-title-EGYPT-FOSS-55',
                'post_name'	=> 'new-test-success-title-EGYPT-FOSS-55',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),                        
            array(
                'post_title' => 'New success 1',
                'post_name'	=> 'success-1',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 2',
                'post_name'	=> 'success-2',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 3',
                'post_name'	=> 'success-3',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 4',
                'post_name'	=> 'success-4',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 5',
                'post_name'	=> 'success-5',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 6',
                'post_name'	=> 'success-6',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 7',
                'post_name'	=> 'success-7',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 8',
                'post_name'	=> 'success-8',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 9',
                'post_name'	=> 'success-9',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 10',
                'post_name'	=> 'success-10',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-success-title-EGYPT-FOSS',
                'post_name'	=> 'new-test-success-title-EGYPT-FOSS',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),             
            array(
                'post_title' => 'New success 11',
                'post_name'	=> 'success-11',
                'post_type'	=> 'success_story',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 12',
                'post_name'	=> 'success-12',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 13',
                'post_name'	=> 'success-13',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 14',
                'post_name'	=> 'success-14',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 15',
                'post_name'	=> 'success-15',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 16',
                'post_name'	=> 'success-16',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 17',
                'post_name'	=> 'success-17',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 18',
                'post_name'	=> 'success-18',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 19',
                'post_name'	=> 'success-19',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 20',
                'post_name'	=> 'success-20',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 21',
                'post_name'	=> 'success-21',
                'post_type'	=> 'success_story',
                'post_status' => 'pending',
                'guid' => ''
            )              
        );

        return $test_news;
    }

    public static function returnExpertThoughts()
    {
        $test_news = array(
            array(
                'post_title' => 'new-test-success-title-EGYPT-FOSS-Ar',
                'post_name' => 'new-test-success-title-EGYPT-FOSS-Ar',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),                 
            array(
                'post_title' => 'new-test-success-title-EGYPT-FOSS-55',
                'post_name' => 'new-test-success-title-EGYPT-FOSS-55',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),                        
            array(
                'post_title' => 'New success 1',
                'post_name' => 'success-1',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 2',
                'post_name' => 'success-2',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 3',
                'post_name' => 'success-3',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 4',
                'post_name' => 'success-4',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 5',
                'post_name' => 'success-5',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 6',
                'post_name' => 'success-6',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 7',
                'post_name' => 'success-7',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 8',
                'post_name' => 'success-8',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 9',
                'post_name' => 'success-9',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 10',
                'post_name' => 'success-10',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-success-title-EGYPT-FOSS',
                'post_name' => 'new-test-success-title-EGYPT-FOSS',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),             
            array(
                'post_title' => 'New success 11',
                'post_name' => 'success-11',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 12',
                'post_name' => 'success-12',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 13',
                'post_name' => 'success-13',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 14',
                'post_name' => 'success-14',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 15',
                'post_name' => 'success-15',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 16',
                'post_name' => 'success-16',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 17',
                'post_name' => 'success-17',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 18',
                'post_name' => 'success-18',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 19',
                'post_name' => 'success-19',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 20',
                'post_name' => 'success-20',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'New success 21',
                'post_name' => 'success-21',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
        );

        return $test_news;
    }
    
    public static function returnExpertThoughtsForEdit()
    {
        $test_thoughts = array(
       
          
            array(
                'post_title' => 'thought 1',
                'post_name' => 'thought-1',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'thought 2',
                'post_name' => 'thought-2',
                'post_type' => 'expert_thought',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'thought 3',
                'post_name' => 'thought-3',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
          array(
                'post_title' => 'thought 4',
                'post_name' => 'thought-4',
                'post_type' => 'expert_thought',
                'post_status' => 'publish',
                'guid' => ''
            ),
        );
      return $test_thoughts;
    }
    
    public static function returnOpenDatasets() {
        $test_datasets = array(
            array(
                'post_title' => 'new-test-dataset-title-EGYPT-FOSS',
                'post_name' => 'new-test-dataset-title-EGYPT-FOSS',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-required-data-only',
                'post_name' => 'new-test-dataset-title-required-data-only',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-one',
                'post_name' => 'new-test-dataset-title-one',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-two',
                'post_name' => 'new-test-dataset-title-two',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-three',
                'post_name' => 'new-test-dataset-title-three',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-four',
                'post_name' => 'new-test-dataset-title-four',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-five',
                'post_name' => 'new-test-dataset-title-five',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-six',
                'post_name' => 'new-test-dataset-title-six',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-seven',
                'post_name' => 'new-test-dataset-title-seven',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-eight',
                'post_name' => 'new-test-dataset-title-eight',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-nine',
                'post_name' => 'new-test-dataset-title-nine',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-ten',
                'post_name' => 'new-test-dataset-title-ten',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-elev',
                'post_name' => 'new-test-dataset-title-elev',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-twlv',
                'post_name' => 'new-test-dataset-title-twlv',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-therth',
                'post_name' => 'new-test-dataset-title-therth',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-fourteen',
                'post_name' => 'new-test-dataset-title-fourteen',
                'post_type' => 'open_dataset',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-fivteen',
                'post_name' => 'new-test-dataset-title-fivteen',
                'post_type' => 'open_dataset',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-sixteen',
                'post_name' => 'new-test-dataset-title-sixteen',
                'post_type' => 'open_dataset',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-seventeen',
                'post_name' => 'new-test-dataset-title-seventeen',
                'post_type' => 'open_dataset',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-eighteen',
                'post_name' => 'new-test-dataset-title-eighteen',
                'post_type' => 'open_dataset',
                'post_status' => 'pending',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-ninteen',
                'post_name' => 'new-test-dataset-title-ninteen',
                'post_type' => 'open_dataset',
                'post_status' => 'publish',
                'guid' => ''
            ),
            array(
                'post_title' => 'new-test-dataset-title-tweenty',
                'post_name' => 'new-test-dataset-title-tweenty',
                'post_type' => 'open_dataset',
                'post_status' => 'pending',
                'guid' => ''
            )              
        );
        return $test_datasets;
    }
    
    public static function returnFOSSPedia()
    {
        $results = array(
            array(
                'title' => 'foss-1',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-2',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-3',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-4',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-5',
                'description'	=> 'foss description'
            ),  
            array(
                'title' => 'foss-6',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-7',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-8',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-9',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-10',
                'description'	=> 'foss description'
            ), 
            array(
                'title' => 'foss-11',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-12',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-13',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-14',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-15',
                'description'	=> 'foss description'
            ), 
            array(
                'title' => 'foss-16',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-17',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-18',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-19',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-20',
                'description'	=> 'foss description'
            ), 
            array(
                'title' => 'foss-21',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-22',
                'description'	=> 'foss description'
            ),
            array(
                'title' => 'foss-23',
                'description'	=> 'foss description'
            )            
        );
        
        return $results;
    }

    public static function returnRequests()
    {
        $results = array(
            array(
                'post_title' => 'request-1',
                'description'	=> 'request description',
                'post_name' => 'request-1',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                
            ),
            array(
                'post_title' => 'request-2',
                'description'	=> 'request description',
                'post_name' => 'request-2',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''               
            ),
            array(
                'post_title' => 'request-3',
                'description'	=> 'request description',
                'post_name' => 'request-3',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-4',
                'description'	=> 'request description',
                'post_name' => 'request-4',
                'post_type' => 'request_center',
                'post_status' => 'pending',
                'guid' => ''  
            ),
            array(
                'post_title' => 'request-5',
                'description'	=> 'request description',
                'post_name' => 'request-5',
                'post_type' => 'request_center',
                'post_status' => 'pending',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'request-6',
                'description'	=> 'request description',
                'post_name' => 'request-6',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-7',
                'description'	=> 'request description',
                'post_name' => 'request-7',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-8',
                'description'	=> 'request description',
                'post_name' => 'request-8',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-9',
                'description'	=> 'request description',
                'post_name' => 'request-9',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-10',
                'description'	=> 'request description',
                'post_name' => 'request-10',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-11',
                'description'	=> 'request description',
                'post_name' => 'request-11',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-12',
                'description'	=> 'request description',
                'post_name' => 'request-12',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-13',
                'description'	=> 'request description',
                'post_name' => 'request-13',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-14',
                'description'	=> 'request description',
                'post_name' => 'request-14',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-15',
                'description'	=> 'request description',
                'post_name' => 'request-15',
                'post_type' => 'request_center',
                'post_status' => 'archive',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-16',
                'description'	=> 'request description',
                'post_name' => 'request-16',
                'post_type' => 'request_center',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-17',
                'description'	=> 'request description',
                'post_name' => 'request-17',
                'post_type' => 'request_center',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-18',
                'description'	=> 'request description',
                'post_name' => 'request-18',
                'post_type' => 'request_center',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-19',
                'description'	=> 'request description',
                'post_name' => 'request-19',
                'post_type' => 'request_center',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-20',
                'description'	=> 'request description',
                'post_name' => 'request-20',
                'post_type' => 'request_center',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'request-21',
                'description'	=> 'request description',
                'post_name' => 'request-21',
                'post_type' => 'request_center',
                'post_status' => 'publish',
                'guid' => ''                  
            )
        );
        
        return $results;
    }

    public static function returnServices()
    {
        $results = array(
            array(
                'post_title' => 'service-22',
                'description'   => 'service description',
                'post_name' => 'service-22',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-23',
                'description'   => 'service description',
                'post_name' => 'service-23',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-24',
                'description'   => 'service description',
                'post_name' => 'service-24',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-25',
                'description'   => 'service description',
                'post_name' => 'service-25',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-26',
                'description'   => 'service description',
                'post_name' => 'service-26',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-27',
                'description'   => 'service description',
                'post_name' => 'service-27',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-28',
                'description'   => 'service description',
                'post_name' => 'service-28',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-29',
                'description'   => 'service description',
                'post_name' => 'service-29',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-30',
                'description'   => 'service description',
                'post_name' => 'service-30',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-1',
                'description'   => 'service description',
                'post_name' => 'service-1',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                
            ),
            array(
                'post_title' => 'service-2',
                'description'   => 'service description',
                'post_name' => 'service-2',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''               
            ),
            array(
                'post_title' => 'service-3',
                'description'   => 'service description',
                'post_name' => 'service-3',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-4',
                'description'   => 'service description',
                'post_name' => 'service-4',
                'post_type' => 'service',
                'post_status' => 'pending',
                'guid' => ''  
            ),
            array(
                'post_title' => 'service-5',
                'description'   => 'service description',
                'post_name' => 'service-5',
                'post_type' => 'service',
                'post_status' => 'pending',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'service-6',
                'description'   => 'service description',
                'post_name' => 'service-6',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-7',
                'description'   => 'service description',
                'post_name' => 'service-7',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-8',
                'description'   => 'service description',
                'post_name' => 'service-8',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-9',
                'description'   => 'service description',
                'post_name' => 'service-9',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-10',
                'description'   => 'service description',
                'post_name' => 'service-10',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-11',
                'description'   => 'service description',
                'post_name' => 'service-11',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-12',
                'description'   => 'service description',
                'post_name' => 'service-12',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-13',
                'description'   => 'service description',
                'post_name' => 'service-13',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-14',
                'description'   => 'service description',
                'post_name' => 'service-14',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-15',
                'description'   => 'service description',
                'post_name' => 'service-15',
                'post_type' => 'service',
                'post_status' => 'archive',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-16',
                'description'   => 'service description',
                'post_name' => 'service-16',
                'post_type' => 'service',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-17',
                'description'   => 'service description',
                'post_name' => 'service-17',
                'post_type' => 'service',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-18',
                'description'   => 'service description',
                'post_name' => 'service-18',
                'post_type' => 'service',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-19',
                'description'   => 'service description',
                'post_name' => 'service-19',
                'post_type' => 'service',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-20',
                'description'   => 'service description',
                'post_name' => 'service-20',
                'post_type' => 'service',
                'post_status' => 'pending',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'service-21',
                'description'   => 'service description',
                'post_name' => 'service-21',
                'post_type' => 'service',
                'post_status' => 'publish',
                'guid' => ''                  
            )
        );
        
        return $results;
    }
    
    public static function returnQuizzes(){
        $results = array(
            array(
                'quiz_title' => 'First Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Second Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),

            array(
                'quiz_title' => 'Third Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Fourth Quiz',
                'questions' => array ('first question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Fifth Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Six Quiz',
                'questions' => array ('first question'   => array('Answer1','Answer2','Answer3'),
                        'Second question'=>array('Answer1','Answer2','Answer3'),
                        'Third question'=>array('Answer1','Answer2','Answer3')
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Seven Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Eight Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Nine Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Ten Quiz',
                'questions' => array ('first question'   =>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Eleven Quiz',
                'questions' => array ('first question'   => array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
                ),
                "post_status" => "publish"
            ),
            array(
                'quiz_title' => 'Twelve Quiz',
                'questions' => array ('first question'   => array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Second question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false),
                        'Third question'=>array('Answer1'=>true,'Answer2'=>false,'Answer3'=>false)
            
                ),
                "post_status" => "publish"
            )
        );

        return $results;
    }

  public static function returnNewsBadgeFirstUser() {
    $test_data = array(
      array(
        'post_title' => 'Badges New News 1',
        'post_name' => 'badges-new-news-1',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
      array(
        'post_title' => 'Badges New News 2',
        'post_name' => 'badges-new-news-2',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
      array(
        'post_title' => 'Badges New News 3',
        'post_name' => 'badges-new-news-3',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
      array(
        'post_title' => 'Badges New News 4',
        'post_name' => 'badges-new-news-4',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
      array(
        'post_title' => 'Badges New News 5',
        'post_name' => 'badges-new-news-5',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
      array(
        'post_title' => 'Badges New News 6',
        'post_name' => 'badges-new-news-6',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
      array(
        'post_title' => 'Badges New News 7',
        'post_name' => 'badges-new-news-7',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
      array(
        'post_title' => 'Badges New News 8',
        'post_name' => 'badges-new-news-8',
        'post_type' => 'news',
        'post_author' => '6',
        'post_status' => 'publish',
        'guid' => ''
      ),
    );
    return $test_data;
  }

    public static function returnNewsBadgeSecondUser() {
        $test_data = array(
          array(
            'post_title' => 'Badges New News 21',
            'post_name' => 'badges-new-news-21',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 22',
            'post_name' => 'badges-new-news-22',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 23',
            'post_name' => 'badges-new-news-23',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 24',
            'post_name' => 'badges-new-news-24',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 25',
            'post_name' => 'badges-new-news-25',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 26',
            'post_name' => 'badges-new-news-26',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 27',
            'post_name' => 'badges-new-news-27',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 28',
            'post_name' => 'badges-new-news-28',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 29',
            'post_name' => 'badges-new-news-29',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
          array(
            'post_title' => 'Badges New News 210',
            'post_name' => 'badges-new-news-210',
            'post_type' => 'news',
            'post_author' => '5',
            'post_status' => 'publish',
            'guid' => ''
          ),
        );
        return $test_data;
    }

    public static function returnTopServices()
    {
        $results = array(
            array(
                'post_title' => 'top-service-1',
                'post_name' => 'top-service-1',
                'post_type' => 'service',
                'post_author' => '5',
                'post_status' => 'publish',
                'guid' => ''     
            ),
            array(
                'post_title' => 'top-service-2',
                'post_name' => 'top-service-2',
                'post_type' => 'service',
                'post_author' => '5',
                'post_status' => 'publish',
                'guid' => ''               
            ),
            array(
                'post_title' => 'top-service-3',
                'post_name' => 'top-service-3',
                'post_type' => 'service',
                'post_author' => '5',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'top-service-4',
                'post_name' => 'top-service-4',
                'post_type' => 'service',
                'post_author' => '5',
                'post_status' => 'publish',
                'guid' => ''   
            ),
            array(
                'post_title' => 'top-service-5',
                'post_name' => 'top-service-5',
                'post_type' => 'service',
                'post_author' => '6',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-6',
                'post_name' => 'top-service-6',
                'post_type' => 'service',
                'post_author' => '6',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'top-service-7',
                'post_name' => 'top-service-7',
                'post_type' => 'service',
                'post_author' => '6',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-8',
                'post_name' => 'top-service-8',
                'post_type' => 'service',
                'post_author' => '6',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'top-service-9',
                'post_name' => 'top-service-9',
                'post_type' => 'service',
                'post_author' => '1',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-10',
                'post_name' => 'top-service-10',
                'post_type' => 'service',
                'post_author' => '1',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-11',
                'post_name' => 'top-service-11',
                'post_type' => 'service',
                'post_author' => '1',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-12',
                'post_name' => 'top-service-12',
                'post_type' => 'service',
                'post_author' => '1',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-13',
                'post_name' => 'top-service-13',
                'post_type' => 'service',
                'post_author' => '2',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-13',
                'post_name' => 'top-service-13',
                'post_type' => 'service',
                'post_author' => '2',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-13',
                'post_name' => 'top-service-13',
                'post_type' => 'service',
                'post_author' => '2',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-13',
                'post_name' => 'top-service-13',
                'post_type' => 'service',
                'post_author' => '2',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-14',
                'post_name' => 'top-service-14',
                'post_type' => 'service',
                'post_author' => '4',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-15',
                'post_name' => 'top-service-15',
                'post_type' => 'service',
                'post_author' => '4',
                'post_status' => 'publish',
                'guid' => ''                   
            ),
            array(
                'post_title' => 'top-service-16',
                'post_name' => 'top-service-16',
                'post_type' => 'service',
                'post_author' => '4',
                'post_status' => 'publish',
                'guid' => ''                  
            ),
            array(
                'post_title' => 'top-service-17',
                'post_name' => 'top-service-17',
                'post_type' => 'service',
                'post_author' => '4',
                'post_status' => 'publish',
                'guid' => ''                 
            )
        );
        
        return $results;
    }

}
