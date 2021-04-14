<?php

/**
 * @file
 * DDB Glossary Import
 * (C) Michael Büchner, Deutsche Nationalbibliothek
 */
 
// for generating valid URL
if (module_exists('pathauto')) {
  module_load_include('inc', 'pathauto');
}

$plugin = array(
  'form' => 'feeds_tamper_ddb_glossary_import_form',
  'validate' => 'feeds_tamper_ddb_glossary_import_validate',
  'callback' => 'feeds_tamper_ddb_glossary_import_callback',
  'name' => 'DDB Glossary Import',
  'multi' => 'loop',
  'category' => 'Other',
);

function feeds_tamper_ddb_glossary_import_form($importer, $element_key, $settings) {
  $form = array();
  
  $replace = array();
  foreach (feeds_tamper_get_unique_source_list($importer) as $source) {
    $replace[$source] = $source;
  }
  
  $form['id_glos'] = array(
    '#type' => 'select',
    '#title' => t('Column name for ID'),
    '#options' => $replace,
    '#required' => TRUE,
    '#default_value' => isset($settings['id_glos']) ? $settings['id_glos'] : '',
    '#description' => t('Please specifiy the column name for the ID.'),
  );
  
  $form['term'] = array(
    '#type' => 'select',
    '#title' => t('Column name for term label'),
    '#options' => $replace,
    '#required' => TRUE,
    '#default_value' => isset($settings['term']) ? $settings['term'] : '',
    '#description' => t('Please specifiy the column name for the term label.'),
  );
  
  $form['note'] = array(
    '#type' => 'select',
    '#title' => t('Column name for term definition'),
    '#options' => $replace,
    '#required' => TRUE,
    '#default_value' => isset($settings['note']) ? $settings['note'] : '',
    '#description' => t('Please specifiy the column name for the term definition.'),
  );
  
  $form['hr'] = array(
    '#markup' => '<hr>'
  );
  
  $form['ns_link'] = array(
    '#type' => 'textfield',
    '#title' => t('Namespace of vocabulary'),
    '#default_value' => isset($settings['ns_link']) ? $settings['ns_link'] : 'http://ddb.vocnet.org/glossar/',
    '#required' => TRUE,
    '#description' => t('Please define the namespace of the vocabulary (e.g. http://ddb.vocnet.org/glossar/)'),
  );
  
  $form['link'] = array(
    '#type' => 'textfield',
    '#title' => t('Link for replacement'),
    '#default_value' => isset($settings['link']) ? $settings['link'] : '<a href="{label_url}"><abbr title="{clean_note}">{label}</abbr></a>',
    '#required' => TRUE,
    '#description' => t('Please specify the HTML snippet for the replacement. Valid place holders are {label}, {label_url}, {clean_note}, {ns} and {id}.'),
  );
  
  $form['textlength'] = array(
    '#type' => 'textfield',
    '#title' => t('Text length'),
    '#default_value' => isset($settings['textlength']) ? $settings['textlength'] : '',
    '#description' => t('Maximum length for a shortened text in {clean_note}. Empty field means full length.'),
  );
  return $form;
}

function feeds_tamper_ddb_glossary_import_validate(&$settings) {
  // Validate $settings
}

function feeds_tamper_ddb_glossary_import_callback($result, $item_key, $element_key, &$field, $settings, $source) {
   
  $field = html_entity_decode($field);
  preg_match_all("/<a[^>]*href=\"([^\" >]*)[^>]*>(.+?)<\/a>/", $field, $links, PREG_SET_ORDER);
  
  $links_array = array();
  
  for ($i = 0; $i < count($links); $i++) {
    $links_array[$i]['complete_html_link'] = $links[$i][0];
    $links_array[$i]['label_link'] = $links[$i][2];
    $links_array[$i]['ns_link'] = substr($links[$i][1], 0, strrpos($links[$i][1], "/") + 1);
    $tmp = substr($links[$i][1], strrpos($links[$i][1], "/") + 1);
    if($tmp) {
      $links_array[$i]['id_glos'] = $tmp;
      $links_array[$i]['pos_array'] = array_search_mult($result->items, $settings['id_glos'], $links_array[$i]['id_glos']);
        
      if($links_array[$i]['pos_array'] > -1) {
        $links_array[$i]['clean_note'] = html_entity_decode($result->items[$links_array[$i]['pos_array']][$settings['note']]);
        $links_array[$i]['clean_note'] = strip_tags($links_array[$i]['clean_note']);
        $links_array[$i]['clean_note'] = preg_replace('/\n/', ' ', $links_array[$i]['clean_note']);
        $links_array[$i]['clean_note'] = preg_replace('/\s\s+/', ' ', $links_array[$i]['clean_note']);
        $links_array[$i]['clean_note'] = trim($links_array[$i]['clean_note']);
        $links_array[$i]['clean_note'] = htmlentities($links_array[$i]['clean_note'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
      }
    }
  }
    
  foreach($links_array as $link) {

    if(isset($link['clean_note']) && startsWith($link['ns_link'], $settings['ns_link'])) {
      if(isset($settings['textlength']) && is_int((int)$settings['textlength']) && (int)$settings['textlength'] > 0) {
        $link['clean_note'] = tokenTruncate($link['clean_note'], (int)$settings['textlength']) . "...";
      }
  
      $new_link = $settings['link'];
      if (module_exists('pathauto')) {
        $new_link = str_replace("{label_url}", pathauto_cleanstring($result->items[$link['pos_array']][$settings['term']]), $new_link);
      } else {
        $new_link = str_replace("{label_url}", urlencode($result->items[$link['pos_array']][$settings['term']]), $new_link);
      }
      $new_link = str_replace("{clean_note}", $link['clean_note'], $new_link);
      $new_link = str_replace("{label}", $link['label_link'], $new_link);
      $new_link = str_replace("{ns}", $link['ns_link'], $new_link);
      $new_link = str_replace("{id}", $link['id_glos'], $new_link);
    
      $field = str_replace($link['complete_html_link'], $new_link, $field);
    } else if(startsWith($link['ns_link'], $settings['ns_link'])) {
        // Term is NOT in glossary
        $field = str_replace($link['complete_html_link'], $link['label_link'], $field);
    } else {
        // Open links to other sites in another window
        $field = str_replace($link['complete_html_link'], str_replace("href=", "target=\"_blank\" href=", $link['complete_html_link']), $field);
    }
  }
}

/* HELPERS */
function startsWith($haystack, $needle) {
  return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function array_search_mult($array, $field, $value) {
  foreach($array as $index => $c) {
    if($c[$field] == $value) {
      return $index;
    }
  }
  return -1;
}

function tokenTruncate($string, $width) {
  $parts = preg_split('/([\s\n]+)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
  $parts_count = count($parts);

  $length = 0;
  $last_part = 0;
  for (; $last_part < $parts_count; ++$last_part) {
    $length += strlen($parts[$last_part]);
    if ($length > $width) { break; }
  }
  return implode(array_slice($parts, 0, $last_part));
}