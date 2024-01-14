<?php

use App\Models\Stuff;
use App\Models\Member;
use App\Models\Product;
use App\Models\SubCategory;

/**
 * generate angka random untuk code
 *
 * @return string
 */
function generateCode()
{
    $min = 1000000000;
    $max = 9999999999;

    $random_number = mt_rand($min, $max);

    return str_pad($random_number, 10, '0', STR_PAD_LEFT);
}

/**
 * mencari member berdasarkan nama dan mereturn idnya
 *
 * @param  mixed $name
 * @return void
 */
function getMember($name)
{
    $member = Member::whereName($name)->first();

    return $member->id;
}

/**
 * mencari sub kategori berdasakan nama dan mereturn idnya
 *
 * @param  mixed $name
 * @return number
 */
function getSubCategory($name)
{
    $subCategory = SubCategory::whereName($name)->first();

    return $subCategory->id;
}

/**
 * mencari barang berdasarkan nama dan mereturn idnya
 *
 * @param  mixed $name
 * @return number
 */
function getStuff($name)
{
    $stuff = Stuff::whereName($name)->first();

    return $stuff->id;
}

/**
 * generate format bulan dan tahun
 * @param mixed $data
 * 
 * @return string
 */
function generateMonthYear($data)
{
    $months = [
        'januari' => '01',
        'februari' => '02',
        'maret' => '03',
        'april' => '04',
        'mei' => '05',
        'juni' => '06',
        'juli' => '07',
        'agustus' => '08',
        'september' => '09',
        'oktober' => '10',
        'november' => '11',
        'desember' => '12',
    ];

    $splitData = explode(' ', $data);

    $monthYear = `{$months[$splitData[0]]}-{$splitData[1]}`;

    return $monthYear;
}


/**
 * mencari produk berdasarkan nama dan mereturn idnya
 *
 * @param  mixed $name
 * @return void
 */
function getProduct($name)
{
    $product = Product::whereName($name)->first();

    return $product->id;
}

/**
 * generate response error server
 * @param mixed $error
 * 
 * @return object
 */
function errorResponse($error)
{
    return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan dengan sistem',
        'error' => $error,
    ], 500);
}

function generateDate($data) {
    $months = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Mei',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    $split = explode('-', $data);

    $newData = "{$split[2]} {$months[$split[1]]} {$split[0]}";

    return $newData;
}