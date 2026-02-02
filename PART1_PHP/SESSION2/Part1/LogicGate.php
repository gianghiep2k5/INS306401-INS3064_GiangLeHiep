<?php
$str = '25.50';
$floatVal = (float)$str; // Ép sang float
$intVal = (int)$floatVal; // Ép sang int

// In ra kiểu dữ liệu và giá trị. Lưu ý: gettype() của float thường trả về "double"
echo "float($floatVal), integer($intVal)";
// Output: float(25.5), integer(25)
?>