<?php
$connection = ldap_connect("ldaps://bdc.novantas.com:636");
$baseDn="dc=Novantas,dc=pri";
$ad_group="cn=Buddypress Group (Infrastructure Team)";
$aryAttribs = array('distinguishedname');
$rscLDAPSearch = ldap_search($connection,$baseDn,$ad_group,$aryAttribs);
var_dump($rscLDAPSearch);
?>
