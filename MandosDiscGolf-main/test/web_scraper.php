<?php

include('simple_html_dom.php');

$pdgaNumber = "71873";
$html = file_get_html('https://www.pdga.com/player/'.$pdgaNumber);

echo $html->find('h1', 0)->plaintext."<br>";

echo $html->find('li[class="current-rating"]', 0)->plaintext."<br>";

echo $html->find('img', 1);

//echo '<img src="https://www.pdga.com/files/styles/large/public/pictures/picture-206011-1619479473.jpg?itok=wVW454wH">';