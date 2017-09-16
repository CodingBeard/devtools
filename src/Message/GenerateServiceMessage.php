<?php

namespace Codingbeard\Devtools\Message;



class GenerateServiceMessage
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
