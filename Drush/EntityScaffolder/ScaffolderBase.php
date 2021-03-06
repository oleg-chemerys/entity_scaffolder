<?php

namespace Drush\EntityScaffolder;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\Logger;
use Symfony\Component\Yaml\Yaml;

class ScaffolderBase extends TemplateManager implements ScaffolderInterface {
  // @see http://php.net/version_compare.
  const VERSION = '0';
  const LOG_FILENAME = '.es.log.yaml';

  const HEADER = '_00_';
  const CONTENT = '_50_';
  const FOOTER = '_99_';

  protected $entity_types;
  protected $template_dir;
  protected $extended_template_dirs;
  protected $code;

  /**
   * ScaffolderBase constructor.
   */
  public function __construct($nameSpace) {
    parent::__construct($nameSpace);
    $this->setConfigDir($this->findConfigDir());
    $this->setConfig($this->loadConfig());
    $this->setEntityTypes($this->findEntityTypes());
    $this->loadTemplateDirs();
  }

  protected function loadConfig() {
    $config = Utils::getConfig($this->getConfigDir() . '/config.yaml');
    $defaults = array(
      'directories' => array(
        'es_helper' => 'sites/all/modules/custom/es_helper',
        'fe_es' => 'sites/all/modules/features/fe_es',
        'fe_es_filters' => 'sites/all/modules/features/fe_es_filters',
      ),
    );
    $config = array_replace_recursive($defaults, $config);
    return $config;
  }

  protected function findConfigDir() {
    if (!($config_dir = drush_get_option('config-dir'))) {
      $config_dir = '.tools/es';
    }
    return $config_dir;
  }

  protected function findEntityTypes() {
    return Utils::getFolders($this->getConfigDir());
  }

  /**
   * Sets the directory from which the scaffold data will be picked up.
   */
  public function setEntityTypes($types) {
    $this->entity_types = $types;
  }

  /**
   * Gets the directory from which the scaffold data will be picked up.
   */
  public function getEntityTypes() {
    return $this->entity_types;
  }

  /**
   * Add to $code.
   */
  public function setCode($module, $filename, $block, $key, $code) {
    $this->code[$module][$filename][$block][$key] = $code;
  }

  /**
   * Gets the $code.
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Start scaffolding.
   */
  public function scaffold() {
    return;
  }

  /**
   * Helper function to perform text replacement.
   */
  function render($template, $replacements) {
    return Utils::renderExtended($this->getExtendedTemplateDirs(), $template, $replacements);
  }

  /**
   * Inject current scaffolder details into config directory.
   */
  function logScaffolderInfo() {
    $info = [
      'version' => $this::VERSION,
    ];
    $yaml = Yaml::dump($info);
    Utils::write($this->getConfigDir() . '/' . $this::LOG_FILENAME, $yaml);
  }

  /**
   * Inject current scaffolder details into config directory.
   */
  function getLoggedScaffolderInfo() {
    return Utils::getConfig($this->getConfigDir() . '/' . $this::LOG_FILENAME);
  }

}
