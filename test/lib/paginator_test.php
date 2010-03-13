<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/search.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/paginator.php';

class PaginatorTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    $this->search = new Search();
    $this->search->total = 618;
    $this->query_string = 'CISOROOT=/p112223&CISOPTR=3323&CISOSTART=1';
    $this->paginator = new Paginator($this->query_string, $this->search, 5);
    $this->paginator->max_records = 20;
  }

  #current_page
  public function testCurrentPage() {
    $this->paginator->offset = 1;
    $this->assertEquals(1, $this->paginator->current_page());

    $this->paginator->offset = 21;
    $this->assertEquals(2, $this->paginator->current_page());

    $this->paginator->offset = 41;
    $this->assertEquals(3, $this->paginator->current_page());

    $this->paginator->offset = 61;
    $this->assertEquals(4, $this->paginator->current_page());

    $this->paginator->offset = 81;
    $this->assertEquals(5, $this->paginator->current_page());
    
    $this->paginator->offset = 101;
    $this->assertEquals(6, $this->paginator->current_page());

    $this->paginator->offset = 201;
    $this->assertEquals(11, $this->paginator->current_page());

    $this->paginator->offset = 221;
    $this->assertEquals(12, $this->paginator->current_page());
  }


  #total_pages
  public function testTotalPages() {
    $this->assertEquals(31, $this->paginator->total_pages());
  }


  #next_link
  public function testNextLink() {
    $this->paginator->offset = 1;
    $this->assertEquals(
      'seek_results.php?CISOROOT=/p112223&amp;CISOPTR=3323&amp;CISOSTART=21',
      $this->paginator->next_link());

    $this->paginator->offset = 21;
    $this->assertEquals(
      'seek_results.php?CISOROOT=/p112223&amp;CISOPTR=3323&amp;CISOSTART=41',
      $this->paginator->next_link());
  }


  #previous_link
  public function testPreviousLink() {
    $this->paginator->offset = 21;
    $this->assertEquals(
      'seek_results.php?CISOROOT=/p112223&amp;CISOPTR=3323&amp;CISOSTART=1',
      $this->paginator->previous_link());

    $this->paginator->offset = 41;
    $this->assertEquals(
      'seek_results.php?CISOROOT=/p112223&amp;CISOPTR=3323&amp;CISOSTART=21',
      $this->paginator->previous_link());
  }


  #pages
  public function testPages() {
    $this->paginator->offset = 1;
    $this->assertEquals(
      array(1 => 1, 2 => 21, 3 => 41, 4 => 61, 5 => 81),
      $this->paginator->pages());

    $this->paginator->offset = 41;
    $this->assertEquals(
      array(1 => 1, 2 => 21, 3 => 41, 4 => 61, 5 => 81),
      $this->paginator->pages());

    $this->paginator->offset = 81;
    $this->assertEquals(
      array(1 => 1, 2 => 21, 3 => 41, 4 => 61, 5 => 81),
      $this->paginator->pages());

    $this->paginator->offset = 101;
    $this->assertEquals(
      array(6 => 101, 7 => 121, 8 => 141, 9 => 161, 10 => 181),
      $this->paginator->pages());

    $this->paginator->offset = 221;
    $this->assertEquals(
      array(11 => 201, 12 => 221, 13 => 241, 14 => 261, 15 => 281),
      $this->paginator->pages());
  }


  #start_page
  public function testStartPage() {
    $this->paginator->offset = 1;
    $this->assertEquals(1, $this->paginator->start_page());

    $this->paginator->offset = 21;
    $this->assertEquals(1, $this->paginator->start_page());

    $this->paginator->offset = 81;
    $this->assertEquals(1, $this->paginator->start_page());

    $this->paginator->offset = 101;
    $this->assertEquals(6, $this->paginator->start_page());

    $this->paginator->offset = 201;
    $this->assertEquals(11, $this->paginator->start_page());
  }


  #is_first_page
  public function testIsFirstPage() {

  }
}
