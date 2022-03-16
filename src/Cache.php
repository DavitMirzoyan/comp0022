<?php   

$mc = new Memcached();                                                             
$mc->addServer("mymemcached", 11211); 
 

function get_from_cache($query){ 
    global $mc; 
    $query_result = $mc->get($query); 
    return $query_result; 
}

function put_to_cache($query, $result){
    global $mc; 
    $new_query = $mc->add($query, $result);  
}



?>