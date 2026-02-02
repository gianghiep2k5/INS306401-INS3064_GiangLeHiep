<?php
$intVal = 5;
$strVal = '5';

// == chỉ so sánh giá trị, === so sánh cả giá trị và kiểu dữ liệu
$equal = ($intVal == $strVal) ? "True" : "False";
$identical = ($intVal === $strVal) ? "True" : "False";

echo "Equal ($equal), Identical ($identical)";
// Output: Equal (True), Identical (False)
?>