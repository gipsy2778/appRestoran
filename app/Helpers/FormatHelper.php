<?php

if (!function_exists('formatAngka')) {
    function formatAngka($angka)
    {
        // Kalau bilangan bulat, tampilkan tanpa desimal
        // Kalau ada desimal, tampilkan maksimal 2 angka tanpa trailing zero
        // Contoh: 1.00 → 1, 1.50 → 1,5, 1.25 → 1,25
        return rtrim(rtrim(number_format($angka, 2, ',', '.'), '0'), ',');
    }
}