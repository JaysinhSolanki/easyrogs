<?php
include("mpdf.php");
$mpdf=new mPDF();
$mpdf->WriteHTML('<h1>Hello world!</h1>');
$mpdf->Output("test.pdf","d");
exit;