<?php

declare(strict_types=1);

class AuthException extends Exception
{
  private string $name;

  public function __construct(string $name, string $message)
  {
    parent::__construct($message);
    $this->name = $name;
  }

  public function getName(): string
  {
    return $this->name;
  }
}
