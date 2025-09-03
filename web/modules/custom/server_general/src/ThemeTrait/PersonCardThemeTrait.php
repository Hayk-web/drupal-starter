<?php

declare(strict_types=1);

namespace Drupal\server_general\ThemeTrait;

/**
 * Helper method for rendering Person Card.
 */
trait PersonCardThemeTrait {
  /**
   * Build a Person card page.
   *
   * @param array $data
   *   Person data.
   *
   * @return array
   *   The render array.
   */
  protected function buildElementPersonCard(array $data): array {
    return [
      '#theme' => 'server_theme_element__person_card',
      '#data' => $data,
    ];
  }

}
