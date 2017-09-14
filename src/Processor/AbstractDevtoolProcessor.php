<?php

namespace Codingbeard\Devtools\Processor;

class AbstractDevtoolProcessor
{
  /** @var \stdClass */
  protected $config;

  /** @var string */
  protected $parentDir;

  /** @var bool */
  protected $isCodingbeard;

  /**
   * AbstractDevtoolProcessor constructor.
   *
   * @param string $parentDir
   */
  public function __construct($parentDir)
  {

    if (stristr($parentDir, 'codingbeard/framework'))
    {
      $this->isCodingbeard = true;
      $file = __DIR__ . '/../../../framework/config/devtools.yaml';
    }
    else
    {
      $this->isCodingbeard = false;
      $file = __DIR__ . '/../../../../../config/devtools.yaml';
    }

    if (is_file($file))
    {
      $config = yaml_parse_file($file);

      if (is_array($config))
      {
        $config = json_decode(json_encode($config['codingbeard']['devtools']));

        $this->config = $config;
      }
      else
      {
        $this->configError();
      }
    }
    else
    {
      $this->configError();
    }

    $this->parentDir = $parentDir;
  }

  /**
   *
   */
  private function configError()
  {
    die(
      "Make sure you are running the command with devtools arguments, do not run E.G. php vendor/codingbeard/devtools/bin/devtools.

If you actually ran it as devtools: Please create a devtools.yaml config with the following structure: " . "
codingbeard:
  devtools:
    project:
      namespace: #E.G. DOGE
      name: #E.G. Doge
      product: #E.G. DOGE\\Module\\Product\\DogeProduct
    abstract:
      cron: #E.G. DOGE\\Module\\Cli\\Task\\Type\\Cron\\AbstractDogeConfigCronTask
      event: #E.G. DOGE\\Module\\Cli\\Task\\Type\\Event\\AbstractDogeEventTask
      interval: #E.G. DOGE\\Module\\Cli\\Task\\Type\\Interval\\AbstractDogeConfigIntervalTask
      single: #E.G. DOGE\\Module\\Cli\\Task\\Type\\Single\\AbstractDogeConfigSingleTask
      worker: #E.G. DOGE\\Module\\Cli\\Task\\Type\\Worker\\AbstractDogeConfigWorkerTask
      model: #E.G. DOGE\\Module\\Phalcon\\Mvc\\AbstractDogeModel
      processor: #E.G. DOGE\\Module\\Processor\\AbstractDogeProcessor
      " . PHP_EOL .
      "And if those files don't exist, they should!" . PHP_EOL
    );
  }

  /**
   * todo do something with this
   * @return int|string
   */
  private function detectNamespace()
  {
    $psr4File = __DIR__ . '/../../../../../composer/autoload_psr4.php';

    if ($psr4File)
    {
      $namespaces1 = include $psr4File;
    }

    $namespaceFile
      = __DIR__ . '/../../../../../composer/autoload_namespaces.php';

    if ($namespaceFile)
    {
      $namespaces2 = include $namespaceFile;
    }

    if (
      isset($namespaces1) && is_array($namespaces1)
      &&
      isset($namespaces2) && is_array($namespaces2)
    )
    {
      $namespaces = array_merge($namespaces1, $namespaces2);
    }
    elseif (isset($namespaces1) && is_array($namespaces1))
    {
      $namespaces = $namespaces1;
    }
    elseif (isset($namespaces2) && is_array($namespaces2))
    {
      $namespaces = $namespaces2;
    }
    else
    {
      return '';
    }

    $bestMatch = '';
    $bestNamespace = '';

    foreach ($namespaces as $namespace => $dir)
    {
      $dirPath = $dir[0];

      if (stripos($this->parentDir, $dirPath) === 0 && strlen($dirPath) > strlen(
          $bestMatch
        )
      )
      {
        $bestMatch = $dirPath;
        $bestNamespace = $namespace;
      }
    }

    return $bestNamespace;
  }
}
