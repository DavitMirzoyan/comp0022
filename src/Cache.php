<?php   

$mc = new Memcached();                                                           
$mc->addServer('my-memcached', 11211); 


function get_from_cache($query){ 
    global $mc; 
    $query_result = $mc->get($query); 
    return $query_result; 
}

function put_to_cache($query, $result){
    global $mc; 
    $new_query = $mc->add($query, $result);  
}

function check_cache($query, $result){
    $query = md5($query);
    #echo $query;
    $cached_result = get_from_cache($query);

    if ($cached_result == ""){
        $getAllResult = array();
        while($row = $result->fetch_assoc()){
            foreach ($row as $field => $value) {
                $getAllResult[] = $value;
            }
            
        }
        echo "put in cache";
        put_to_cache($query, $getAllResult, MEMCACHE_COMPRESSED); 

        return $getAllResult;
    }else{
        echo "take from cache";

        return $cached_result;
    }
}



?>