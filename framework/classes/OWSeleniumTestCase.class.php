<?php
define('OWS_AJAX_SELENIUM_TEST_CASE_WAITING_COUNT', 30);
define('OWS_AJAX_SELENIUM_TEST_CASE_WAITING_SECONDS', 1);

class OWSeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase {

  protected $arg = array();
  protected $domain;
  protected $login;
  protected $password;
  protected $ajaxWaitingCount;
  protected $ajaxWaitingSeconds;

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
    if (!getenv('AJAX_WAITING_COUNT')) {
      $this->ajaxWaitingCount = OWS_AJAX_SELENIUM_TEST_CASE_WAITING_COUNT;
    }
    else {
      $this->ajaxWaitingCount = getenv('AJAX_WAITING_COUNT');
    }
    if (!getenv('AJAX_WAITING_SECONDS')) {
      $this->ajaxWaitingSeconds = OWS_AJAX_SELENIUM_TEST_CASE_WAITING_SECONDS;
    }
    else {
      $this->ajaxWaitingSeconds = getenv('AJAX_WAITING_SECONDS');
    }
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

    for ($i = 0 ; $i < $this->ajaxWaitingCount ; $i++) {
      if ($this->isElementPresent($element)) {
        parent::click($element);
        $element_present = TRUE;
        break;
      }
      else {
        sleep($this->ajaxWaitingSeconds);
      }
    }
    if(!$element_present) {
      $this->assertTrue(FALSE, $element . " does not exist, or the AJAX request timed out.");
    }

  }

}
