<?php
class SeekResultsHelper {
  public function search_fields_without_alias($search) {
    return array_diff_key($search->form_fields(), array('CISOROOT' => TRUE));
  }
}
