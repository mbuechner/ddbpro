<?php
/**
 * @file
 * Default theme implementation for assets.
 *
 * Available variables:
 * - $content: An array of comment items. Use render($content) to print them all, or
 *   print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $title: The (sanitized) asset label.
 * - $url: Direct url of the current asset if specified.
 * - $page: Flag for the full page state.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. By default the following classes are available, where
 *   the parts enclosed by {} are replaced by the appropriate values:
 *   - entity-asset
 *   - {asset}-{BUNDLE}
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_entity()
 * @see template_preprocess_asset()
 * @see template_process()
 */
?>
<?php
render($content);
if($content['field_asset_image']['#object']->asset_options['align'] !== "center") {
  foreach($content['field_asset_image'] as $key => $item) {
    if(substr($key, 0, 1) !== "#" && isset($item['#image_style']) && isset($item['#item']['uri'])) {
      $img_url = image_style_path($item['#image_style'], $item['#item']['uri']);
      $img_info = image_get_info($img_url);
      if(!isset($width) || $img_info['width'] > $width) {
        $width = $img_info['width'];
      }
    }
  }
}
$re = "/<a[^>]*(title=\\\"[^\\\"]*\\\").*>/i";
preg_match_all($re, $content['field_asset_image']['#children'], $matches, PREG_OFFSET_CAPTURE);
if(isset($matches[1][0]) && !is_null($matches[1][0])) {
  $position = $matches[1][0][1];
  $length = strlen($matches[1][0][0]);
  $replacement = $matches[1][0][0] . " data-" . $matches[1][0][0];
  $content['field_asset_image']['#children'] = substr_replace($content['field_asset_image']['#children'], $replacement, $position, $length);
}
?>
<figure class="figure" <?php isset($width) ? print "style=\"max-width:" . $width . "px;\"" : "" ?><?php print $content_attributes; ?>>
  <?php print $content['field_asset_image']['#children'] ?>
  <?php if (isset($content['field_asset_image_description']['#children'])): ?>
    <figcaption class="figure-caption">
      <?php print $content['field_asset_image_description']['#children'] ?>
    </figcaption>
  <?php endif; ?>
</figure>