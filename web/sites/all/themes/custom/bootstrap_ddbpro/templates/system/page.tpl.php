<?php
/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see bootstrap_preprocess_page()
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see bootstrap_process_page()
 * @see template_process()
 * @see html.tpl.php
 *
 * @ingroup themeable
 */
?>
<header id="navbar" class="<?php print $navbar_classes; ?> navbar-tworow hidden-print">
  <div class="<?php print $container_class; ?>">
    <div class="navbar-header">
      <?php if ($logo): ?>
        <a class="logo navbar-btn pull-left" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
          <img class="img-responsive" src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
        </a>
      <?php endif; ?>
      <?php if (!empty($site_name)): ?>
        <a class="navbar-brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>"><?php print $site_name; ?></a>
      <?php endif; ?>
      <?php if (!empty($primary_nav) || !empty($secondary_nav) || !empty($page['navigation'])): ?>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only"><?php print t('Toggle navigation'); ?></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      <?php endif; ?>
    </div>
    <div class="collapse navbar-collapse">
      <div class="navbar-right navbar-ddbsearch">
        <form accept-charset="UTF-8" id="search-form-header" method="get" action="<?php print $GLOBALS['base_url'];?>/suche" class="nav navbar-form">
          <div class="input-group">
            <input class="form-control" id="edit-query-header" name="query" placeholder="Suchen in DDBpro" type="text" maxlength="128" />
            <div class="input-group-btn">
              <button class="btn btn-default" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </form>
      </div>
      <?php if (!empty($primary_nav) || !empty($secondary_nav) || !empty($page['navigation'])): ?>       
        <?php if (!empty($primary_nav)): ?>
          <?php print str_replace("<ul class=\"","<ul class=\"navbar-left ", render($primary_nav)); ?>
        <?php endif; ?>
        <?php if (!empty($secondary_nav)): ?>
          <?php print render($secondary_nav); ?>
        <?php endif; ?>
        <?php if (!empty($page['navigation'])): ?>
          <?php print render($page['navigation']); ?>
        <?php endif; ?>
      <?php endif; ?>
      <ul class="nav navbar-nav navbar-right navbar-ddblogin">
        <?php if ($user->uid > 0): ?>
          <li class="dropdown<?php if (arg(0) == 'user' && arg(1) == $user->uid):?> active-trail<?php endif;?>">
            <span data-toggle="dropdown" class="dropdown-toggle nolink"><?php print $user->name; ?><span class="caret"></span></span>
            <ul class="dropdown-menu" role="menu">
              <li<?php if (arg(0) == 'user' && arg(1) == $user->uid):?> class="active"<?php endif;?>><?php print l(t('Mein Profil'), drupal_get_path_alias('user/' . $user->uid));?></li>
              <li><?php print l(t('Abmelden'), drupal_get_path_alias('user/logout'));?></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="text-uppercase<?php if (arg(0) == 'user' && arg(1) == $user->uid):?> active-trail<?php endif;?>"><?php print l(t('Login'), 'user', array('attributes'=>array('title'=>''))); ?></li>
        <?php endif; ?>
      </ul>  
    </div>
  </div>
</header>
<?php if (!empty($breadcrumb)): ?>
<div class="breadcrumb-container container hidden-print">
  <?php print $breadcrumb; ?>
</div>
<?php endif;?>
<div class="main-container <?php print $container_class; ?>">
    <?php if (!empty($page['sidebar_first'])): ?>
    <aside class="col-xs-12 col-md-3 hidden-print">
      <?php print render($page['sidebar_first']); ?>
    </aside>
    <?php endif; ?>
    <section id="main-content"<?php print $content_column_class; ?>>
      <?php if (!empty($page['highlighted'])): ?>
        <div class="highlighted jumbotron"><?php print render($page['highlighted']); ?></div>
      <?php endif; ?>
      <?php print render($title_prefix); ?>
      <?php print render($page['header']); ?>
      <?php if (!empty($title) && (arg(0) == 'node' || arg(0) == 'user') && is_numeric(arg(1)) && !drupal_is_front_page()): ?>
        <h1 class="page-header"><?php print $title; ?>&#160;<small><i title="Stabiler Link" id="ddbpropermalink" class="fa fa-link permalink hidden-print"></i></small></h1>
        <div id="ddbpropermalinkcontent" class="hide">
        <form>
          <div class="input-group">
              <input type="text" name="permalink" id="ddbpropermalinkinput" class="form-control input-md" value="<?php print t($GLOBALS['base_url'] ."/". current_path()); ?>" placeholder="<?php print t($GLOBALS['base_url'] ."/". current_path()); ?>" id="copy-input" />
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="ddbpropermalinkcopybtn">
                  <i class="far fa-clipboard" aria-hidden="true"></i>
                </button>
              </span>
            </div>
          </form>
        </div>
      <?php elseif (!empty($title)): ?>
        <h1 class="page-header"><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print render($title_suffix); ?>
      <?php print $messages; ?>
      <?php if (!empty($tabs)): ?>
        <?php print render($tabs); ?>
      <?php endif; ?>
      <?php if (!empty($page['help'])): ?>
        <?php print render($page['help']); ?>
      <?php endif; ?>
      <?php if (!empty($action_links)): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
      <?php print render($page['content']); ?>
    </section>
    <?php if (!empty($page['sidebar_second'])): ?>
    <aside class="col-xs-12 col-md-3">
      <?php print render($page['sidebar_second']); ?>
    </aside>
    <?php endif; ?>
</div>
<?php if (!empty($page['footer'])): ?>
<footer class="footer hidden-print <?php print $container_class; ?>">
  <div class="row small">
    <div class="col-sm-10 clearfix"><?php print render($page['footer']); ?></div>
    <div class="col-sm-2 sociallinks">
      <span>
        <i class="fab fa-facebook"></i> <a href="https://facebook.com/ddbkultur" title="Facebook" class="facebooklink" target="_blank">Facebook</a>
      </span>
      <span>
        <i class="fab fa-twitter"></i> <a href="https://twitter.com/ddbkultur"  title="Twitter" class="twitterlink" target="_blank">Twitter</a>
      </span>
    </div>
  </div>
</footer>
<?php endif; ?>