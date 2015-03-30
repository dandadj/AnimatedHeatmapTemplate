<?php
    require mysql;
    $servername = "yanen.li";
    $username = "wang296";
    $password = "Whn1984523";
    $dbname = "HotelReviews";

    // Connecting, selecting database
    $link = mysql_connect($servername, $username, $password)
        or die('Could not connect: ' . mysql_error());
    echo 'Connected successfully';
    mysql_select_db($dbname) or die('Could not select database');

    // Performing SQL query
    $query = 'SELECT * FROM HotelsNew limit 10';
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    // Printing results in HTML
    echo "<table>\n";
    while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
            echo "\t\t<td>$col_value</td>\n";
        }
        echo "\t</tr>\n";
    }
    echo "</table>\n";

    // Free resultset
    mysql_free_result($result);

    // Closing connection
    mysql_close($link);
?>