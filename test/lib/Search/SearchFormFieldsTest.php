<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchFormFields extends PHPUnit_Framework_TestCase {
  public function testShouldOutputSimpleSearch() {
    $search = Search::from_param_string("s=simple");
    $fields = $search->form_fields();
    $this->assertEquals('/p4006coll2,/p4006coll3,/p4006coll7,/p4006coll4,/p4006coll5,/p4006coll8,/p4006coll10,/p4006coll15,/p4006coll17,/p129401coll0,/p129401coll3,/p129401coll7,/p129401coll10', $fields['CISOROOT']);
    $this->assertEquals('simple', $fields['CISOBOX1']);
    $this->assertEquals('any', $fields['CISOOP1']);
  }

  public function testShouldOutputFullForm() {
    $params = array(
      'CISOROOT' => '/p4006coll3',
      'CISOBOX1' => 'street car',
      'CISOOP1' => 'exact',
      'CISOFIELD1' => 'title',
      'CISOBOX2' => 'Lansing',
      'CISOOP2' => 'any',
      'CISOFIELD2' => 'title',
      'CISOBOX3' => 'station',
      'CISOOP3' => 'any',
      'CISOFIELD3' => 'subjec',
      'CISOSTART' => '1,21'
    );
    $search = Search::from_params($params);
    $fields = $search->form_fields();
    $this->assertEquals($params, $fields);
  }

  public function testShouldOverrideGivenParams() {
    $params = array(
      'CISOROOT' => '/p4006coll3',
      'CISOBOX1' => 'street car',
      'CISOOP1' => 'exact',
      'CISOFIELD1' => 'title',
      'CISOBOX2' => 'Lansing',
      'CISOOP2' => 'any',
      'CISOFIELD2' => 'title',
      'CISOBOX3' => 'station',
      'CISOOP3' => 'any',
      'CISOFIELD3' => 'subjec',
      'CISOSTART' => '1,21'
    );
    $search = Search::from_params($params);
    $fields = $search->form_fields(array('CISOROOT' => 'abracadabra'));
    $new_params = $params;
    $new_params['CISOROOT'] = 'abracadabra';
    $this->assertEquals($new_params, $fields);
  }
}
