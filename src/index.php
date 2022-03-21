<?php
    include 'database_tables_data.php';

    $mc = new Memcached();                                                         
    $mc->addServer("my-memcached", 11211);                                              

    echo "
    		<h1 style='text-align:center;'>
			<a href='web.php'> Web</a> 
		</h1>
    ";
?>