<?php

if (!function_exists('varlen')) {
    /**
     * Get length of a variable/string
     */
    function varlen($str)
	{
		if (is_string($str)) {
            // Jika string, gunakan strlen
            return strlen($str);
        } elseif (is_array($str)) {
            // Jika array, gunakan count
            return count($str);
        } elseif (is_object($str)) {
            // Jika object, hitung properti publiknya
            return count(get_object_vars($str));
        } elseif (is_null($str)) {
            // Null dianggap tidak memiliki panjang
            return 0;
        } elseif (is_bool($str)) {
            // Boolean tidak punya panjang, bisa dianggap 0 atau 1
            return (int)$str; // true = 1, false = 0
        } elseif (is_int($str) || is_float($str)) {
            // Panjang angka dianggap jumlah digit dalam bentuk string
            return strlen((string)$str);
        } else {
			if($str == null) {
				$str = '';
			}
			return strlen($str);
		}
	}
}
