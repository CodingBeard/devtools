<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateServiceMessage extends AbstractMessage
{
  /**
   * @devtoolsOverwritable
   * @var string
   */
  protected $serviceCode;

  /**
   * @devtoolsOverwritable
   * @var string
   */
  protected $providers;

  /**
   * @devtoolsOverwritable
   * @return string
   */
  public function getServiceCode()
  {
    return $this->serviceCode;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $serviceCode
   *
   * @return GenerateServiceMessage
   */
  public function setServiceCode($serviceCode)
  {
    $this->serviceCode = $serviceCode;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   * @return string
   */
  public function getProviders()
  {
    return $this->providers;
  }

  /**
   * @devtoolsOverwritable

   *
*@param string $providers
   *
*@return GenerateServiceMessage
   */
  public function setProviders($providers)
  {
    $this->providers = $providers;

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
