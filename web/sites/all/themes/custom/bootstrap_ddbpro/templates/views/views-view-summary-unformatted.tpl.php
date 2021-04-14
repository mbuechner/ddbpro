<?php

/**
 * @file
 * View for DDB glossary
 *
 * Author: Michael Büchner (DDB)
 *
 * @ingroup views_templates
 */
?>

<?php print !empty($options['inline']) ? '<ul class="views-summary views-summary-unformatted pagination list-unstyled">' : '<ul class="views-summary views-summary-unformatted list-group list-unstyled">'; ?>

<?php
/* Add ALL to link menu */
$num_all_records = 0;
foreach ($rows as $id => $row) {
  $num_all_records += $row->count;
}
if($num_all_records > 0 ) {
  $title = t('Alle');
  $rows[sizeof($rows) + 1] = new stdClass;
  $rows[sizeof($rows)]->title_truncated = $title;
  $rows[sizeof($rows)]->num_records = $num_all_records;
  $rows[sizeof($rows)]->field_field_date = array();
  $rows[sizeof($rows)]->field_body = array();
  $rows[sizeof($rows)]->field_field_alternatives = array();
  $rows[sizeof($rows)]->link = $title;
  $rows[sizeof($rows)]->url = substr_replace(strtolower($rows[0]->url), 'all', -1, strlen($rows[0]->link));
  $rows[sizeof($rows)]->count = $num_all_records;

  if((base_path() . current_path()) == $rows[sizeof($rows)]->url) {
    $row_classes[sizeof($rows)] = 'active';
  }
}
?>

<?php foreach ($rows as $id => $row): ?>
  <?php $liclass = !empty($row_classes[$id]) ? $row_classes[$id] : '';
        $liclass = !empty($options['inline']) ? trim($liclass) : trim($liclass) . ' list-group-item';
        $liclass = !empty($liclass) ? ' class="' . trim($liclass) . '"' : ''; 
        if(!empty($options['count']) && $row->count == 1) {
          $tooltip = $row->count . " Fachbegriff";
        } else if(!empty($options['count'])) {
          $tooltip = $row->count . " Fachbegriffe";
        }
  ?>
  <li<?php print $liclass; ?>><a class="link-unstyled" data-placement="top" data-toggle="tooltip" data-original-title="<?php print $tooltip; ?>" href="<?php print $row->url; ?>" title="<?php print $tooltip; ?>"><?php if (!empty($row->separator)) { print $row->separator; } ?><?php print $row->link; ?></a></li>
<?php endforeach; ?>
<?php print '</ul>'; ?>



















