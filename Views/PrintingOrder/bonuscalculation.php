<?php
include '../../public/includes/db_connect.php';

function calculateBonus($total_sales) {
    if ($total_sales > 450) return 150;
    if ($total_sales > 350) return 120;
    if ($total_sales > 280) return 80;
    if ($total_sales > 200) return 50;
    return 0;
}
?>
