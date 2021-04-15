<?php
/**
 * HTML5 Meta tag for charset
 *
 */
function bootstrap_ddbpro_html_head_alter(&$head_elements) {
  $head_elements['system_meta_content_type']['#attributes'] = array(
    'charset' => 'utf-8'
  );
}

/**
 * Adding Bootstrap resposive class to images and remove height/width
 * from inline styles because we're using Bootstrap's img-thumbnail class
 *
 */
function bootstrap_ddbpro_preprocess_image(&$vars) {
  foreach (array('width', 'height') as $key) {
    unset($vars[$key]);
  }
}

/**
 * Wrap short appointment list on start page into a Bootstrap panel
 * Wrap Ansprechpartner list into a Bootstrap panel
 * Wrap Downloads block into Boostrap panel
 */
function bootstrap_ddbpro_preprocess_views_view(&$vars) {
  if ($vars['name']=='calendar' && $vars['display_id']=='appointmentshortlist') {
    $vars['rows'] = '<div class="panel panel-default">' . $vars['rows'] . '</div>';
  } else if ($vars['name']=='ansprechpartner' && $vars['display_id']=='ansprechpartnerlist') {
    $vars['rows'] = '<div class="panel panel-default">' . $vars['rows'] . '</div>';
  } else if ($vars['name']=='downloads' && $vars['display_id']=='downloadsblock') {
    $vars['rows'] = '<div class="panel panel-default">' . $vars['rows'] . '</div>';
  }
}

/**
 * Generate title with facet values for FAQ, Team and Termine page
 *
 */
function bootstrap_ddbpro_views_pre_render(&$view) {
  if($view->name == 'team' || $view->name == 'faqs' || $view->name == 'termine') {
    if ($searchers = facetapi_get_active_searchers()) {
      $terms_title = array();
      $searcher = reset($searchers);
      $adapter = facetapi_adapter_load($searcher);
      foreach ($adapter->getAllActiveItems() as $item) {
        $term = taxonomy_term_load($item['value']);
        $terms_title[] = $term->name;
      }
      if(count($terms_title) > 0) {
        $view->build_info['title'] = $view->display[$view->current_display]->display_options['title'];
        if(empty($view->build_info['title'])) {
          $view->build_info['title'] = $view->display['default']->display_options['title'];
        }
        $view->build_info['title'] .= " <small>(" . implode(', ', $terms_title) . ")</small>";
      }
    }
  }
}

function bootstrap_ddbpro_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'views_exposed_form') {
    $form['#attributes']['class'][] = t('form-inline');
    
    $view = $form_state['view'];
    if ($view->name == 'suche' && $view->current_display == 'page') {
      $element = &$form['query'];
      $element['#title_display'] = t('invisible');
      if(isset($form['#info']['filter-search_api_views_fulltext']['description'])) {
        $element['#attributes']['data-toggle'] = t('tooltip');
        $element['#attributes']['data-placement'] = t('top');
        $element['#attributes']['title'] = $form['#info']['filter-search_api_views_fulltext']['description'];
        unset($form['#info']['filter-search_api_views_fulltext']['description']);
      }
      
      if(isset($form['#info']['filter-search_api_views_fulltext']['label'])) {
        $element['#attributes']['placeholder'] = $form['#info']['filter-search_api_views_fulltext']['label'];
        unset($form['#info']['filter-search_api_views_fulltext']['label']);
      }
      
      $element = &$form['type'];
      $element['#title_display'] = t('invisible');
      
      if(isset($form['#info']['filter-type']['description'])) {
        $element['#attributes']['data-toggle'] = t('tooltip');
        $element['#attributes']['data-placement'] = t('top');
        $element['#attributes']['title'] = $form['#info']['filter-type']['description'];
        unset($form['#info']['filter-type']['description']);
      }
      
      $element = &$form['submit'];
      $element['#name'] = t('submit');
      $element['#attributes']['style'] = t('margin-top: 0px;');
    }
  }
}
?>