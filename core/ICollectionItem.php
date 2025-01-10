<?php

declare(strict_types=1);

interface ICollectionItem
{
  public static function createById(mysqli $db, string|int $id);
  public function getId(): string;
  public function getType(): string;
  public function getTitle(): string;
  public function getSubtitle(): string;
  public function getSliderSubtitle(): string;
  public function getLink(): string;
  public function getCover(): string;
}
