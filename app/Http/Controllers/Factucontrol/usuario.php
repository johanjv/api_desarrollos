<?php 
require_once("config.php"); 
function usuarioda($user,$pass, $nombre){ 
     $ldaprdn = trim($user).'@'.DOMINIO;  
     $ldappass = trim($pass);  
     $ds = DOMINIO;  
     $dn = DN;   
     $puertoldap = 389;  
     $ldapconn = ldap_connect($ds,$puertoldap); 
       ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION,3);  
       ldap_set_option($ldapconn, LDAP_OPT_REFERRALS,0);  
       $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);  
       if ($ldapbind){ 
         $filter="(|(displayname=*".trim($nombre)."))";
         $serch = ldap_search($ldapconn, $dn, "displayname=*".$nombre."*");
         $info = @ldap_get_entries($ldapconn, $serch);  
         $array = $info; 
       
        
       }else{  
             $array=0; 
       }  
     ldap_close($ldapconn);  
     return $array; 
}