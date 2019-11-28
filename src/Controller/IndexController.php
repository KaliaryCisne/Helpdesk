<?php

declare(strict_types=1);

namespace Root\Controller;

final class IndexController extends AbstractController
{
  public function indexAction(): void
  {
      $this->render('Index/index');
  }

  public function notFoundAction(): void
  {
      $this->render('template/404');
  }
}
