<?php

/**
 * @file
 * Widget that renders facets as a list of clickable links.
 */

/**
 * Extends FacetapiWidget to provide DDBpro widget.
 */
class FacetapiWidgetDDBpro extends FacetapiWidget {

  /**
   * Implements FacetapiWidget::__construct().
   *
   * Overrides constructor to reset the key.
   */
  public function __construct($id, array $realm, FacetapiFacet $facet, stdClass $settings) {
    parent::__construct($id, $realm, $facet, $settings);
    $this->key = $facet['name'];
  }

  /**
   * Implements FacetapiWidget::execute().
   */
  public function execute() {
    // Sets the key, captures the facet's render array.
    $element = &$this->build[$this->facet['field alias']];

    $element = array(
      '#theme' => 'item_list',
      '#items' => $this->buildListItems($element),
      '#attributes' => $this->build['#attributes'],
      '#attached' => array(
        'css' => array(
          //drupal_get_path('module', 'facetapi_ddbpro') . '/facetapi_ddbpro.css',
        ),
      ),
    );
    $element['#attributes']['class'][] = 'list-ddbstyle';
  }

  /**
   * Recursive function that converts the render array into an array that can be
   * passed to theme_item_list().
   *
   * @param array $build
   *   The facet's render array.
   *
   * @return array
   *   The "items" parameter for theme_item_list().
   */
  function buildListItems($build) {
    $items = array();
    foreach ($build as $value => $item) {
      $row = array('class' => array());

      // Initializes variables passed to theme hook.
      $variables = array(
        'text' => $item['#markup'],
        'path' => $item['#path'],
        'count' => $item['#count'],
        'options' => array(
          'attributes' => array(
            'class' => array(($item['#active']) ? 'active' : 'inactive')),
          'html' => $item['#html'],
          'query' => $item['#query'],
        ),
      );
      
      // Adds the facetapi-zero-results class to items that have no results.
      if (!$item['#count']) {
        $variables['options']['attributes']['class'][] = 'facetapi-zero-results';
      }

      // Add an ID to identify this link.
      $variables['options']['attributes']['id'] = drupal_html_id('facetapi-link');
      
      // If the item has no children, it is a leaf.
      if (empty($item['#item_children'])) {
        $row['class'][] = 'leaf';
      }
      else {
        // If the item is active or the "show_expanded" setting is selected,
        // show this item as expanded so we see its children.
        if ($item['#active'] || !empty($settings['show_expanded'])) {
          $row['class'][] = 'expanded';
          $row['children'] = $this->buildListItems($item['#item_children']);
        }
        else {
          $row['class'][] = 'collapsed';
        }
      }

      // Themes the link, adds row to items.
      $hook = ($item['#active']) ? 'facetapi_ddbpro_link_active' : 'facetapi_ddbpro_link_inactive';
      $row['data'] = theme($hook, $variables);
      $items[] = $row;
    }

    return $items;
  }

  /**
   * Implements FacetapiWidget::settingsForm().
   */
  public function settingsForm(&$form, &$form_state) {
    $form['widget']['widget_settings']['ddbpro'][$this->id]['ddbpro_levels'] = array(
      '#type' => 'select',
      '#title' => t('Number of levels'),
      '#default_value' => $this->settings->settings['ddbpro_levels'],
      '#options' => drupal_map_assoc(range(2, 10)),
      '#description' => t('The number of levels between the lowest facet count and the highest. The size of the item in the tag cloud is determined by its level, so more levels will yield a wider variety of sizes.'),
      '#states' => array(
        'visible' => array(
          'select[name="widget"]' => array('value' => $this->id),
        ),
      ),
    );
  }

  /**
   * Implements FacetapiWidget::getDefaultSettings().
   */
  public function getDefaultSettings() {
    return array('ddbpro_levels' => 6);
  }
}
