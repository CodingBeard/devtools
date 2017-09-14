<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Devtools\Message\GenerateEventMessage;
use Codingbeard\Framework\Module\Processor\Exception\InvalidMessageValueException;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateServiceMessage;

class GenerateServiceProcessor extends AbstractDevtoolProcessor
{
  /** @var \stdClass */
  protected $serviceConfig;

  /**
   * @devtoolsOverwritable
   * @return GenerateServiceMessage
   */
  public function getMessageClass()
  {
    return new GenerateServiceMessage;
  }

  /**
   * @param GenerateServiceMessage $message
   *
   * @return bool
   */
  public function process(GenerateServiceMessage $message)
  {
    $this->validate($message);

    $configDir = __DIR__ . '/../../../../../config/';

    $serviceConfigs = [];

    foreach (scandir($configDir) as $configFile)
    {
      if (stristr($configFile, '-services.yaml'))
      {
        $serviceConfigs[$configFile] = $configDir . $configFile;
      }
    }

    if (!count($serviceConfigs))
    {
      die('There are no services configs E.G. defaults-services.yaml in ' . $configDir . PHP_EOL);
    }

    if (isset($serviceConfigs['defaults-services.yaml']))
    {
      $defaults = $serviceConfigs['defaults-services.yaml'];
    }
    else
    {
      $defaults = $serviceConfigs[0];
    }

    $config = yaml_parse_file($defaults);

    if (!isset($config['codingbeard']['service']['services'][$message->getServiceCode()]))
    {
      die('There is no config for ' . $message->getServiceCode() . ' in the config file: ' . $defaults);
    }

    if (is_array($config))
    {
      $this->serviceConfig = json_decode(
        json_encode(
          $config['codingbeard']['service']['services'][$message->getServiceCode()]
        )
      );
    }
    else
    {
      die($defaults . ' is an invalid yaml file' . PHP_EOL);
    }

    $serviceDir = __DIR__ . '/../../../../../service/' . strtoupper($message->getServiceCode()) . '/';

    $serviceName = sprintf(
      '%s%sService',
      ucfirst(strtolower($this->config->project->name)),
      $message->getServiceCode()
    );

    $providers = [];
    $relations = [];
    $uses = [];

    if ($message->getProviders())
    {
      if (stristr($message->getProviders(), ','))
      {
        foreach (explode(',', $message->getProviders()) as $provider)
        {
          $providers[] = (object)[
            'name' => $provider,
          ];
        }
      }
      else
      {
        $providers[] = (object)[
          'name' => $message->getProviders(),
        ];
      }
    }

    if (isset($this->serviceConfig->relations) && is_object($this->serviceConfig->relations))
    {
      foreach ($this->serviceConfig->relations as $relationType => $related)
      {
        if (is_array($related))
        {
          foreach ($related as $relation)
          {
            if (is_array($relation))
            {
              $name = '[';

              foreach ($relation as $relationship)
              {
                $name .= sprintf(
                  '%s%sService::CODE, ',
                  ucfirst(strtolower($this->config->project->name)),
                  $relationship
                );

                $use = sprintf(
                  '%s\Service\%s\%s%sService',
                  $this->config->project->namespace,
                  $relationship,
                  ucfirst(strtolower($this->config->project->name)),
                  $relationship
                );

                if (!in_array($use, $uses))
                {
                  $uses[] = $use;
                }
              }

              $relations[] = [
                'name' => substr($name, 0, -2) . ']',
                'type' => $relationType,
              ];
            }
            else
            {
              $relations[] = [
                'name' => sprintf(
                  '%s%sService::CODE',
                  ucfirst(strtolower($this->config->project->name)),
                  $relation
                ),
                'type' => $relationType,
              ];

              $use = sprintf(
                '%s\Service\%s\%s%sService',
                $this->config->project->namespace,
                $relation,
                ucfirst(strtolower($this->config->project->name)),
                $relation
              );

              if (!in_array($use, $uses))
              {
                $uses[] = $use;
              }
            }
          }
        }
      }
    }

    $generator = new DevtoolTemplateGenerator($serviceDir);

    $generator->generateFile(
      $serviceName . '.php',
      'Service',
      'service',
      [
        'project'   => [
          'namespace' => rtrim($this->config->project->namespace, '\\'),
        ],
        'service'   => [
          'name'      => $serviceName,
          'code'      => $message->getServiceCode(),
          'providers' => $providers,
          'relations' => $relations,
          'uses'      => $uses,
          'aliases'   => isset($this->serviceConfig->alias) ? $this->serviceConfig->alias : []
        ],
        'event'     => [
          'prefix' => $message->getServiceCode(),
        ],
      ]
    );

    $events = [
      'prePurchaseEvent' => true,
      'postPurchaseEvent' => true,
      'preOrderProcessEvent' => true,
      'postOrderProcessEvent' => true,
      'serviceAddedEvent' => false,
      'servicePaidEvent' => false,
      'serviceSuspendedEvent' => false,
      'serviceUnsuspendedEvent' => false,
      'serviceCancelledEvent' => false,
      'serviceUncancelledEvent' => false,
    ];

    foreach ($events as $eventName => $sync)
    {
      $eventClassName = sprintf(
        '%s%s',
        $message->getServiceCode(),
        ucfirst($eventName)
      );

      $namespace = sprintf(
        '%s\\Service\\%s',
        $this->config->project->namespace,
        $message->getServiceCode()
      );

      if ($sync)
      {
        $src = $serviceDir . '/Processor/Event';
        $abstractClass = end(explode('\\', $this->config->abstract->serviceSyncEvent));
        $abstractNamespace = $this->config->abstract->serviceSyncEvent;
      }
      else
      {
        $src = $serviceDir . '/Task/Event';
        $abstractClass = end(explode('\\', $this->config->abstract->serviceAsyncEvent));
        $abstractNamespace = $this->config->abstract->serviceAsyncEvent;
      }

      $generator = new DevtoolTemplateGenerator($src);

      $generator->generateFile(
        $eventClassName . '.php',
        'Service',
        $eventName,
        [
          'message' => [
            'identifier' => [
              'group' => $message->getServiceCode() . 'Service',
              'name'  => str_replace('Event', '', $eventName),
            ],
          ],
          'event'   => [
            'namespace' => $namespace,
            'name'      => $eventClassName,
            'abstract'  => [
              'name'     => $abstractClass,
              'namespace' => $abstractNamespace,
            ],
          ],
        ]
      );
    }

    foreach ($providers as $provider)
    {
      $generator = new DevtoolTemplateGenerator(
        $serviceDir . '/Provider/'
      );

      $providerName = sprintf(
        '%s%sProvider',
        $provider->name,
        $message->getServiceCode()
      );

      $providerNamespace = sprintf(
        '%s\\Service\\%s\\Provider\\%s',
        $this->config->project->namespace,
        $message->getServiceCode(),
        $provider->name
      );

      $generator->generateFile(
        $providerName . '.php',
        'Service',
        'provider',
        [
          'provider' => [
            'namespace' => $providerNamespace,
            'name'      => $providerName,
            'abstract'  => [
              'class'     => end(
                explode('\\', $this->config->abstract->serviceProvider)
              ),
              'namespace' => $this->config->abstract->serviceProvider,
            ],
          ],
        ]
      );
    }

    echo sprintf(
      '      %sService:
        prePurchase:
          enabled: false
        postPurchase:
          enabled: false
        preOrderProcess:
          enabled: false
        postOrderProcess:
          enabled: false
        serviceAdded:
          enabled: false
          legacy: false
        servicePaid:
          enabled: false
          legacy: false
        serviceSuspended:
          enabled: false
          legacy: false
        serviceUnsuspended:
          enabled: false
          legacy: false
        serviceCancelled:
          enabled: false
          legacy: false
        serviceUncancelled:
          enabled: false
          legacy: false%s',
      $message->getServiceCode(),
      PHP_EOL
    );

    echo sprintf(
      '              %sService:
          serviceAdded:
            description: Service was added
            retryAfterMinutes: 5
            maxWorkerLimit: 5
            batchAmount: 50
          servicePaid:
            description: Service was paid
            retryAfterMinutes: 5
            maxWorkerLimit: 5
            batchAmount: 50
          serviceSuspended:
            description: Service was suspended
            retryAfterMinutes: 5
            maxWorkerLimit: 5
            batchAmount: 50
          serviceUnsuspended:
            description: Service was unsuspended
            retryAfterMinutes: 5
            maxWorkerLimit: 5
            batchAmount: 50
          serviceCancelled:
            description: Service was cancelled
            retryAfterMinutes: 5
            maxWorkerLimit: 5
            batchAmount: 50
          serviceUncancelled:
            description: Service was uncancelled
            retryAfterMinutes: 5
            maxWorkerLimit: 5
            batchAmount: 50%s',
      $message->getServiceCode(),
      PHP_EOL
    );

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateServiceMessage $message
   *
   * @throws \Exception
   * @throws InvalidMessageValueException
   * @returns GenerateServiceMessage
   */
  public function validate(GenerateServiceMessage $message)
  {
    if (empty($message->getServiceCode()))
    {
      throw new \Exception('serviceCode');
    }

    return $message;
  }

}

/**
devtools generate:service ADTIME
devtools generate:service BKUP MyPcBackup
devtools generate:service CLOUD JustCloud
devtools generate:service CTCT Rosetta
devtools generate:service DEDIP Hal
devtools generate:service DESEBO
devtools generate:service DIR
devtools generate:service DOMAIN CodingbeardDomain
devtools generate:service DOMPRI CodingbeardDomain
devtools generate:service ECOM Ecwid
devtools generate:service EMAIL Logicboxes
devtools generate:service LOCK Hal,Sitelock
devtools generate:service LOGO
devtools generate:service MANSSL
devtools generate:service OYUS
devtools generate:service PLAN Site,Cpanel
devtools generate:service PRISUP
devtools generate:service SEOEBO
devtools generate:service SEOPRO
devtools generate:service SES
devtools generate:service SETP
devtools generate:service SHIST
devtools generate:service SOCIAL Uplift
devtools generate:service SPAM Hal
devtools generate:service SSL Hal
devtools generate:service STPER
devtools generate:service STPRO
devtools generate:service STSMB
devtools generate:service SUPER
devtools generate:service ULTSEO Ultimate
 */
