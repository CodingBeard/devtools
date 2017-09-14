<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateContainerMessage extends AbstractMessage
{
  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $buildFile;


  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getBuildFile()
  {
    return $this->buildFile;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $buildFile
   *
   * @return GenerateModuleMessage
   */
  public function setBuildFile($buildFile)
  {
    $this->buildFile = $buildFile;

    return $this;
  }


  /**
   * @devtoolsOverwritable
   */
  public function preSerialize()
  {
  }

  /**
   * @devtoolsOverwritable
   */
  public function postUnserialize()
  {
  }
}
