<?php

namespace MediaWiki\Extension\PkgStore;

use MWException;
use Parser, PPFrame, OutputPage, Skin;

/**
 * Class MW_EXT_Badge
 */
class MW_EXT_Badge
{
  /**
   * Get badge.
   *
   * @param $badge
   *
   * @return array
   */
  private static function getBadge($badge): array
  {
    $get = MW_EXT_Kernel::getYAML(__DIR__ . '/store/badge.yml');
    return $get[$badge] ?? [] ?: [];
  }

  /**
   * Get badge type.
   *
   * @param $badge
   * @param $type
   *
   * @return array
   */
  private static function getType($badge, $type): array
  {
    $badge = self::getBadge($badge);
    return $badge[$type] ?? [] ?: [];
  }

  /**
   * @param $badge
   * @param $type
   * @return string
   */
  private static function getID($badge, $type): string
  {
    $type = self::getType($badge, $type) ? self::getType($badge, $type) : '';
    return $type['id'] ?? '' ?: '';
  }

  /**
   * Get badge icon.
   *
   * @param $badge
   * @param $type
   *
   * @return string
   */
  private static function getIcon($badge, $type): string
  {
    $type = self::getType($badge, $type) ? self::getType($badge, $type) : '';
    return $type['icon'] ?? '' ?: '';
  }

  /**
   * Get badge category.
   *
   * @param $badge
   * @param $type
   *
   * @return string
   */
  private static function getCategory($badge, $type): string
  {
    $type = self::getType($badge, $type) ? self::getType($badge, $type) : '';
    return $type['category'] ?? '' ?: '';
  }

  /**
   * Get badge content.
   *
   * @param $badge
   * @param $type
   *
   * @return string
   */
  private static function getContent($badge, $type): string
  {
    $type = self::getType($badge, $type) ? self::getType($badge, $type) : '';
    return $type['content'] ?? '' ?: '';
  }

  /**
   * Register tag function.
   *
   * @param Parser $parser
   *
   * @return void
   * @throws MWException
   */
  public static function onParserFirstCallInit(Parser $parser): void
  {
    $parser->setFunctionHook('badge', [__CLASS__, 'onRenderTag']);
  }

  /**
   * Render tag function.
   *
   * @param Parser $parser
   * @param string $badge
   * @param string $type
   *
   * @return string|null
   */
  public static function onRenderTag(Parser $parser, string $badge = '', string $type = ''): ?string
  {
    // Argument: badge.
    $getBadge = MW_EXT_Kernel::outClear($badge ?? '' ?: '');
    $outBadge = MW_EXT_Kernel::outNormalize($getBadge);

    // Argument: type.
    $getType = MW_EXT_Kernel::outClear($type ?? '' ?: '');
    $outType = MW_EXT_Kernel::outNormalize($getType);

    // Check arguments, set error category.
    if (!self::getBadge($outBadge) || !self::getType($outBadge, $outType)) {
      $parser->addTrackingCategory('mw-badge-error-category');

      return null;
    }

    // Get ID.
    $getID = self::getID($outBadge, $outType);
    $outID = $getID;

    // Get icon.
    $getIcon = self::getIcon($outBadge, $outType);
    $outIcon = $getIcon;

    // Get category.
    $getCategory = self::getCategory($outBadge, $outType);
    $outCategory = $getCategory;

    // Get content.
    $getContent = self::getContent($outBadge, $outType);
    $outContent = $getContent;

    // Add badge category.
    $parser->addTrackingCategory('mw-badge-' . $outCategory);

    // Out HTML.
    $outHTML = '<div class="mw-badge mw-badge-' . $outBadge . ' mw-badge-' . $outType . '>';
    $outHTML .= '<div class="mw-badge-body>';
    $outHTML .= '<div class="mw-badge-icon"><i class="' . $outIcon . '"></i></div>';
    $outHTML .= '<div class="mw-badge-content">' . MW_EXT_Kernel::getMessageText('badge', $outContent) . '</div>';
    $outHTML .= '</div>';
    $outHTML .= '</div>';

    // Out parser.
    return $outHTML;
  }

  /**
   * Load resource function.
   *
   * @param OutputPage $out
   * @param Skin $skin
   *
   * @return void
   */
  public static function onBeforePageDisplay(OutputPage $out, Skin $skin): void
  {
    $out->addModuleStyles(array('ext.mw.badge.styles'));
  }
}
