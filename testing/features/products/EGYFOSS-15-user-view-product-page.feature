Feature: User view a published product on the system
  In order to view a published product on the system
  As a user
  I need to navigate to product page and view all product's details

    @javascript @not_implemented
    Scenario Outline: Adding new product from the list page with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with "<title>" , "<developer>" , "<desc>" , "<func>" , "<ind>" , "<usage>" , "<ref>" , "<link_to_source>" , "<type>" , "<tech>" , "<platform>" , "<license>" and "<keywords>" 
        #And I press "publish"
        #Then I should see "product published. "
        
        Examples:
        | title | developer | desc | func | ind | usage | ref | link_to_source | type | tech | platform | license | keywords | 
        | MYIT | Maii | MYIT MYIT MYIT| QA Engineer | IT | A very nice MYIT | ref1 | http://www.espace.com.eg | type1 | tech1 | platform1 | license1 | MYIT |
        | CDex | Eslam | [Be careful during the installation: ]CDex is a CD-Ripper, extracting digital audio data from an Audio CD. The application supports many Audio encoders, including WAV, MP3, AAC, Ogg Vorbis, FLAC, VQF, Musepack, APE, and many others.Several languages are supported. | Software Engineer | Software | A very nice software | ref2 | www.google.com | type2 | tech2 | platform2 | license2 | CDex |
        | Memerise | Ashraf | Memerise Memerise Memerise | Software Engineer | Computer engineering | A very nice Memrise | ref3 | http://www.facebook.com | type3 | tech3 | platform3 | license3 | Memrise |

    @javascript @not_implemented
    Scenario Outline: Adding new product from the list page with only required inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with "<title>" , "" , "<desc>" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "publish"
        Then I should see "product published. "  
        
        Examples:
        | title | desc |
        | Jingo | Jingo is a very nice software, try to use it now.  |
        | Twitter | Twitter is a very nice software, try to use it now. |

    @javascript @not_working
    Scenario: A user navigating to view product page from products list page
        Given I am on "/en/"
        When I resize window with height 800 and width 1024 in px
        And I follow "Products"
        And I follow "software-engineering"
        And I follow "productTest_9with_taxs_en"
        Then I should be on "/en/products/producttest_9with_taxs_en/"

    @javascript @not_implemented
    Scenario: A user viewing a product with all its details inserted
        Given I am on "/en/products/producttest_9with_taxs_en/"
        Then I should see "software-engineering"
        And I should see "application"
        And I should see "[Be careful during the installation: ]CDex is a CD-Ripper, extracting digital audio data from an Audio CD. The application supports many Audio encoders, including WAV, MP3, AAC, Ogg Vorbis, FLAC, VQF, Musepack, APE, and many others.Several languages are supported."
        And I should see "php"
        And I should see "linux"
        And I should see "MIT"
        And I should see "ref2"
        And I should see "www.google.com"
        And I should see "java"
        And I should see "tech2"
        And I should see "platform2"
        And I should see "license2"
        And I should see "CDex"

    @javascript @invalid
    Scenario: A user viewing a product with only required data
        Given I am on "/en/products/Jingo"
        Then I should see "Jingo"
        And I should see "Jingo is a very nice software, try to use it now."
        And I should see "Not Specified"
        And I should see "No screenshots for this product"