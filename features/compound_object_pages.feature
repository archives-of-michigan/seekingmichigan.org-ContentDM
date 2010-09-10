Feature: Compound Object Pages
  As a User
  I want to see all pages listed for a compound object
  So that I can quickly browse to a page

  Scenario: Typical Object
    When I go to /compound_object_pages.php
    Then I should see "Page 1"
    And I should see "Page 2"
