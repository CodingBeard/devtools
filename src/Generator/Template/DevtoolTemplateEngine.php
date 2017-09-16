<?php

namespace Codingbeard\Devtools\Generator\Template;

use Phalcon\DI;
use Phalcon\Mvc\User\Module;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;

class DevtoolTemplateEngine
{
  /** @var DI */
  private $di;

  /**
   * DevtoolEngine constructor.
   */
  public function __construct()
  {
    $di = new DI();

    $filters = [
      'ucfirst',
      'lcfirst',
      'strtoupper',
      'strtolower',
      'ucwords'
    ];

    $di->setShared(
      'view',
      function () use ($di, $filters)
      {
        $view = new View();
        $view->registerEngines(
          [
            ".twig" => function ($view, $di) use ($filters)
            {
              $volt = new Volt($view, $di);
              $volt->setOptions(
                [
                  'compiledPath'   => "/tmp/",
                  'compiledAlways' => true,
                ]
              );

              $compiler = $volt->getCompiler();

              foreach ($filters as $filter)
              {
                $compiler->addFilter($filter, $filter);
              }

              $compiler->addFilter('camel', function ($resolvedArgs, $exprArgs) {
                return 'str_replace(" ", "", ucwords(str_replace("_", " ", ' . $resolvedArgs . ')))';
              });

              $compiler->addFunction('substr', function ($resolvedArgs, $exprArgs) {
                return 'substr(' . $resolvedArgs . ')';
              });

              return $volt;
            },
          ]
        );

        return $view;
      }
    );

    $this->di = $di;
  }

  /**
   * @param string $folder
   * @param string $file
   * @param array  $variables
   *
   * @return string
   */
  public function render($folder, $file, $variables)
  {
    /** @var View $view */
    $view = clone $this->di->getShared('view');

    $variables = json_decode(json_encode($variables));

    $view->setViewsDir(__DIR__ . '/../../Template/');
    foreach ($variables as $key => $value)
    {
      $view->setVar($key, $value);
    }
    $view->start();
    $view->render($folder, $file);
    $view->finish();

    return $view->getContent();
  }
}
