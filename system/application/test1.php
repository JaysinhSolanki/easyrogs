<?php

$num = 2;
$num = numToWord($num);

function numToWord($num)
{
    $this_word = array('One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen','Twenty','Twenty-One', 'Twenty-Two', 'Twenty-Three','Twenty-Four','Twenty-Five');
    if($num <= 26)
    {
        $num--;
        $num = $this_word[$num];
    }
    return $num;
 }
echo $num;
?>