<?php
/**
 *  _____ _     _                   _    ____ ___
 * |_   _| |__ (_)_______ _ __     / \  |  _ \_ _|
 *   | | | '_ \| |_  / _ \ '__|   / _ \ | |_) | |
 *   | | | | | | |/ /  __/ |     / ___ \|  __/| |
 *   |_| |_| |_|_/___\___|_|    /_/   \_\_|  |___|
 *
 *      Transformando vidas, realizando sonhos.
 *
 * @author Thizer
 */
defined('TRAVIS') || define('TRAVIS', (bool) getenv('TRAVIS'));

use PHPUnit\Framework\TestCase;

class Cmthizer1StartTest extends TestCase
{
  private static $instance;
  
  public function testInstance() {
    self::$instance = new CmThizer();
    $this->assertInstanceOf('CmThizer', self::$instance, 'Instancia de CmThizer');
  }
  
  public function testStep1() {
    self::$instance->step1();
    
    $plugins = self::$instance->getPlugins();
    foreach (array_keys($plugins) as $name) {
      $this->assertNotNull(self::$instance->getPlugin($name), 'Retorna plugin '.$name);
    }
  }
}