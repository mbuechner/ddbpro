<?php
/**
 * @file
 * Default theme implementation to display the basic html structure of a single
 * Drupal page.
 *
 * Variables:
 * - $css: An array of CSS files for the current page.
 * - $language: (object) The language the site is being displayed in.
 *   $language->language contains its textual representation.
 *   $language->dir contains the language direction. It will either be 'ltr' or
 *   'rtl'.
 * - $rdf_namespaces: All the RDF namespace prefixes used in the HTML document.
 * - $grddl_profile: A GRDDL profile allowing agents to extract the RDF data.
 * - $head_title: A modified version of the page title, for use in the TITLE
 *   tag.
 * - $head_title_array: (array) An associative array containing the string parts
 *   that were used to generate the $head_title variable, already prepared to be
 *   output as TITLE tag. The key/value pairs may contain one or more of the
 *   following, depending on conditions:
 *   - title: The title of the current page, if any.
 *   - name: The name of the site.
 *   - slogan: The slogan of the site, if any, and if there is no title.
 * - $head: Markup for the HEAD section (including meta tags, keyword tags, and
 *   so on).
 * - $styles: Style tags necessary to import all CSS files for the page.
 * - $scripts: Script tags necessary to load the JavaScript files and settings
 *   for the page.
 * - $page_top: Initial markup from any modules that have altered the
 *   page. This variable should always be output first, before all other dynamic
 *   content.
 * - $page: The rendered page content.
 * - $page_bottom: Final closing markup from any modules that have altered the
 *   page. This variable should always be output last, after all other dynamic
 *   content.
 * - $classes String of classes that can be used to style contextually through
 *   CSS.
 *
 * @see bootstrap_preprocess_html()
 * @see template_preprocess()
 * @see template_preprocess_html()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" lang="<?php print $language->language; ?>" dir="<?php print $language->dir;?>" <?php print trim($rdf_namespaces);?>>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php print $head; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="referrer" content="origin-when-cross-origin" />
<link rel="apple-touch-icon" sizes="57x57" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="60x60" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon" sizes="72x72" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="76x76" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="114x114" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="120x120" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="144x144" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="152x152" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-152x152.png" />
<link rel="apple-touch-icon" sizes="180x180" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/apple-touch-icon-180x180.png" />
<link rel="icon" type="image/png" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/favicon-194x194.png" sizes="194x194" />
<link rel="icon" type="image/png" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/android-chrome-192x192.png" sizes="192x192" />
<link rel="icon" type="image/png" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/favicon-16x16.png" sizes="16x16" />
<link rel="manifest" href="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/manifest.json" />
<meta name="msapplication-TileColor" content="#a5003b" />
<meta name="msapplication-TileImage" content="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/mstile-144x144.png" />
<meta name="msapplication-config" content="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/img/browserconfig.xml" />
<meta name="theme-color" content="#a5003b">
<title><?php print $head_title; ?></title>
<?php print $styles; ?>
<!--[if lt IE 9]>
  <script src="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/js/html5shiv.min.js"></script>
  <script src="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/js/respond.min.js"></script>
<![endif]-->
<?php print $scripts; ?>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>
  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
  <script>jQuery(document).delegate('.region-content .figure a[rel="lightbox"]','click',function(event){event.preventDefault();jQuery(this).ekkoLightbox({loadingMessage:'Laden...'});});</script>
  <script src="<?php print $GLOBALS['base_url'] . "/" . path_to_theme(); ?>/js/ddbpropermalink.min.js"></script>
</body>
</html>