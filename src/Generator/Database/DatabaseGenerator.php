<?php

namespace Codingbeard\Devtools\Generator\Database;

use Phalcon\Config\Adapter\Ini;

class DatabaseGenerator
{
  /** @var string */
  protected $configDir;

  /** @var array * */
  protected $configFiles
    = [
      'migrate.ini',
      'shared/defaults-shared.ini',
      'shared/dev-shared.ini',
      'shared/design-shared.ini',
      'shared/latest-shared.ini',
      'shared/uat-shared.ini',
      'shared/qa-shared.ini',
      'shared/prod-shared.ini',
      '../../../../app/releases/dev/config/migrate.ini',
      '../../../../app/releases/dev/config/shared/defaults-shared.ini',
      '../../../../app/releases/dev/config/shared/dev-shared.ini',
      '../../../../app/releases/dev/config/shared/design-shared.ini',
      '../../../../app/releases/dev/config/shared/latest-shared.ini',
      '../../../../app/releases/dev/config/shared/uat-shared.ini',
      '../../../../app/releases/dev/config/shared/qa-shared.ini',
      '../../../../app/releases/dev/config/shared/prod-shared.ini',
    ];

  /** @var string */
  protected $name;

  /** @var string */
  protected $copyName = 'accounts';

  /**
   * DatabaseGenerator constructor.
   *
   * @param string $configDir
   * @param string $name
   * @param string $copyName
   */
  public function __construct($configDir, $name, $copyName = 'accounts')
  {
    $this->configDir = $configDir;
    $this->name = $name;
    $this->copyName = $copyName;
  }

  /**
   *
   */
  public function generate()
  {
    foreach($this->configFiles as $path)
    {
      $filePath = $this->configDir . $path;
      $file = file_get_contents($filePath);

      $config = new Ini($filePath);

      $copyKey = $this->copyName . 'DB';
      $newKey = $this->name . 'DB';

      if (!isset($config->$copyKey))
      {
        echo "Cannot copy a DB configuration that doesn't exist: '{$this->copyName}' in file: '{$path}'" . PHP_EOL;
        continue;
      }

      if (isset($config->$newKey))
      {
        echo "Config already exists for: '{$this->name}' in file: '{$path}'" . PHP_EOL;
        continue;
      }

      $copyConfig = $config->$copyKey->toArray();

      if (isset($copyConfig['dbname']))
      {
        $copyConfig['dbname'] = $this->name;
      }

      $newDb = [
        $newKey => $copyConfig
      ];

      $ini = $this->arrayToIni($newDb);

      $position = stripos($file, sprintf('[%s]', $copyKey));

      $file = substr($file, 0, $position) . $ini . substr($file, $position);

      file_put_contents($filePath, $file);
    }
  }

  /**
   * @param $array
   *
   * @return string
   */
  private function arrayToIni($array)
  {
    $ini = '';
    foreach($array as $groupName => $group)
    {
      $ini .= sprintf('[%s]%s', $groupName, PHP_EOL);

      foreach($group as $key => $value)
      {
        $ini .= sprintf('%s = "%s"%s', $key, $value, PHP_EOL);
      }

      $ini .= PHP_EOL;
    }

    return $ini;
  }

}
