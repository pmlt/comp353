<?php

use ra\domains as rd;

class User extends Relation
{
  public static function attributes()
  {
    return array(
      'title' => rd\enum(User::getTitles(), null),
      'firstname' => rd\str(),
      'middlename' => rd\str(null),
      'lastname' => rd\str(),
      'country' => rd\country(User::getCountries(), null),
      'org' => rd\enum(User::getOrganizations(), null),
      'dept' => rd\str(),
      'address' => rd\str(null),
      'city' => rd\str(null),
      'prov' => rd\str(null),
      'postal_code' => rd\postal_code(null),
      'email' => rd\email()
    );
  }
  public static function getTitles() {
    array('Mr.','Ms.','Mrs.','Dr.');
  }
  public static function getCountries() {
    return array('ca' => 'Canada', 'us' => 'United States of America');
  }
  public static function getOrganizations() {
    return array('Concordia','ICANN');
  }
}

?>
