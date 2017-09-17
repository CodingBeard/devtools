<?php

namespace Codingbeard\Devtools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefactorReplaceReturnThisCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('refactor:replaceReturnThis')
      ->setDescription(
        'Recursively replaces all docblock return tags using $this with the class name usage: devtools refactor:replaceReturnThis path/to/folder/'
      )
      ->addArgument(
        'folder',
        InputArgument::REQUIRED,
        'Folder to refactor'
      );
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   *
   * @throws \Exception
   * @return bool
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $folder = $input->getArgument('folder');

    $path = CURRENT_DIR . rtrim($folder, '/') . '/';

    if (!is_dir($path))
    {
      throw new \Exception("Path provided is not a folder '{$path}'");
    }

    $classes = $this->getClasses($path);

    foreach($classes as $filePath => $contents)
    {
      $contents = str_replace(
        '@return $this',
        '@return ' . pathinfo($filePath)['filename'],
        $contents
      );

      file_put_contents($filePath, $contents);

      echo 'Updating ' . $filePath . PHP_EOL;
    }
  }

  /**
   * @param            $path
   *
   * @return array
   */
  private function getClasses($path)
  {
    $path = rtrim($path, '/') . '/';
    $files = scandir($path);

    $classes = [];

    foreach ($files as $file)
    {
      if ($file == '.' || $file == '..')
      {
        continue;
      }

      if (is_dir($path . $file))
      {
        $classes = array_merge($classes, $this->getClasses($path . $file));
        continue;
      }

      if (stripos($file, '.') === false)
      {
        continue;
      }

      $extension = strtolower(pathinfo($path . $file)['extension']);

      if (!in_array($extension, ['php']))
      {
        continue;
      }

      $name = pathinfo($path . $file)['filename'];

      $contents = file_get_contents($path . $file);

      if (stristr($contents, '@return $this'))
      {
        if (preg_match("#class {$name}\\s#i", $contents))
        {
          $classes[$path . $file] = $contents;
        }
        elseif (preg_match("#interface {$name}\\s#i", $contents))
        {
          $classes[$path . $file] = $contents;
        }
        elseif (preg_match("#trait {$name}\\s#i", $contents))
        {
          $classes[$path . $file] = $contents;
        }
        else
        {
          continue;
        }
      }
      else
      {
        continue;
      }
    }

    return $classes;
  }

}
