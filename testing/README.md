EgyptFOSS Testing
====

Testing Scenarios with Behat, Mink, and Selenium

***************************************************************************
## Installation instructions:
### 1. Install Composer
  > `curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer`

### 2
### 2.1 Install MailCatcher
  > `gem install mailcatcher`
  
### 2.2 Start MailCatcher Service
  > `mailcatcher`    

### 2.3 To access your mail through the browser: http://127.0.0.1:1080/

### 3 (If need to run the test scenarios over headless browser)
### 3.1 Install PhantomJS 2.1.1 : [The Installation Guide](/testing/phantomjs-guide.md)
  
### 3.2 Can change default browser inside behat.yml by changing browser_name
  > `browser_name: phantomjs`       

### 4. Create a composer.json file in your directory
```bash
<code>
{
  "require": {
    "behat/behat": "3.*@stable",
    "behat/mink": "1.7.*@stable",
    "behat/mink-extension": "@stable",
    "behat/mink-goutte-driver": "@stable",
    "behat/mink-selenium2-driver": "*",
    "emuse/behat-html-formatter": "0.1.*",
    "behat/mink-browserkit-driver": "*",
    "behat/symfony2-extension": "2.0.*@dev"
  },
  "require-dev": {
      "tpayne/behat-mail-extension": "~1.0"
  }
}
</code>
```
### 5. Run composer install

### 6. Create a behat.yml file
```bash
<code>
default:
    autoload:
        '': %paths.base%/features/bootstrap
    suites:
        default:
            contexts:
                - FeatureContext
    formatters: 
      html:
        output_path: %paths.base%/scenarios/
    extensions:
       # Behat\Symfony2Extension: ~
        tPayne\BehatMailExtension\ServiceContainer\MailExtension:
            driver: mailcatcher
            base_url: localhost # optional
            http_port: 1080 # optional
        Behat\MinkExtension:
            base_url: http://egyptfoss.com
            browser_name: firefox
            javascript_session: selenium2
            #default_session: 'symfony2'
            sessions:
                goutte: # fast, CLI, browser, no javascript support
                    goutte: ~
                selenium2: # fast, CLI, opens up a browser
                    selenium2: ~
            #    symfony2:
            #        symfony2: ~
        emuse\BehatHTMLFormatter\BehatHTMLFormatterExtension:
            name: html
            renderer: Twig #,Behat2
            file_name: Index
            print_args: true
            print_outp: true
            loop_break: true
</code>
```

### 7. Initialize your Behat project with 
  > `vendor/bin/behat --init`

### 8. Create a feature file, e.g. test.feature
```bash
<code>
Feature: Drupal.org search
  In order to find modules on Drupal.org
  As a Drupal user
  I need to be able to use Drupal.org search

  @javascript
  Scenario: Searching for "behat"
    Given I go to "http://drupal.org"
    When I search for "behat"
    Then I should see "Behat Drupal Extension"
</code>
```

### 9. Edit FeatureContext.php to extend from MinkContext. 
Note: You will need to add the use statement, use Behat\MinkExtension\Context\MinkContext.

### 10
### 10.1 Download Selenium Server
  > `http://selenium-release.storage.googleapis.com/2.48/selenium-server-standalone-2.48.2.jar`

### 10.2 Run 
  > `java -jar /path/to/selenium-server-standalone-2.37.0.jar`

### 10.3 Add 
  > `selenium2: ~ to your behat.yml`

### 10.4 Add a @javascript tag above your scenario

### 10.5 make sure which default browser you need firefox or phantomjs by changing browser_name in behat.yml

### 10.6 In case of running phantomJs, can take screenshot by adding this line in the scenario, the images are saved on /tmp folder with random name printed on the terminal
  > `And take screenshot`

### 10.7 In case of running Chrome, need to download chromedriver
  > `https://sites.google.com/a/chromium.org/chromedriver/downloads`
And run selenium with the following command
  > `java -Dwebdriver.chrome.driver=/path/to/chromedriver -jar /path/to/selenium-server-standalone-2.37.0.jar`

### 11 Run 
  > `vendor/bin/behat --format pretty`
      Or
  > `vendor/bin/behat --format progress`

### 12 Note
#### 12.1 Resetting the database happens on the start of any suite,It drops the databases (foss and foss-pedia) then re-create them again through 
 > `./fossdb.sh -s `

#### 12.2 Reset database function is commented, and can uncomment in FeatureContext.php inside prepare() function

#### 12.3 You need to change mysql database password inside /db-config.php file