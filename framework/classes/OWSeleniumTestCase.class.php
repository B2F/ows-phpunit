<?php
define('OWS_AJAX_SELENIUM_TEST_CASE_WAITING_COUNT', 30);
define('OWS_AJAX_SELENIUM_TEST_CASE_WAITING_SECONDS', 1);

class OWSeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase {

  protected $arg = array();
  protected $domain;
  protected $login;
  protected $password;

  /**
   * Initialize the test class with arguments and environement variables 
   * from a phpunit .xml configuration file.
   * @see /usr/share/php/PHPUnit/Extensions/SeleniumTestCase.php
   */
  public function __construct($name = NULL, array $data = array(), $dataName = '') {
    parent::__construct($name, $data, $dataName);
    $this->parseParams();
    $this->domain = getenv('DOMAIN');
    $this->login = getenv('LOGIN');
    $this->password = getenv('PASSWORD');
  }

  protected function setUp()
  {
    $this->setBrowser("*firefox");
    $this->setBrowserUrl($this->domain);
  }

  /*
   * Experimental: 
   *   Fill the $arg array with phpunit arguments values, argument name 
   *   must begin with "-". You can then access the array in your test methods.
   *   This is not working with directories (test suites), only unique tests such
   *   as "phpunit TestCase.php -name value"
   */
  private function parseParams() {
    foreach ($_SERVER['argv'] as $key => $value) {
      // Les arguments commencent par '-'.
      if (strpos($value, '-') === 0) {
        $name = substr($value, 1);
        // On ajoute dans arg la valeur suivant l'argument.
        $this->arg[$name] = $_SERVER['argv'][$key+1];
      }
    }
  }

  /**
   * Override PHPUnit_Extensions_SeleniumTestCase with AJAX support.
   */
  public function click($element) {
 
    $element_present = FALSE;

    for ($i = 0 ; $i < OWS_AJAX_SELENIUM_TEST_CASE_WAITING_COUNT ; $i++) {
      if ($this->isElementPresent($element)) {
        parent::click($element);
        $element_present = TRUE;
        break;
      }
      else {
        sleep(OWS_AJAX_SELENIUM_TEST_CASE_WAITING_SECONDS);
      }
    }
    if(!$element_present) {
      $this->assertTrue(FALSE, $element . " does not exist, or the AJAX request timed out.");
    }

  }

}
