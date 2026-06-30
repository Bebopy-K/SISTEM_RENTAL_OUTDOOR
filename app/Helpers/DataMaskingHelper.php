<?php
namespace App\Helpers;

class DataMaskingHelper
{
    public static function maskEmail($email)
    {
        if (!$email || $email === '-') return '-';
        if (!str_contains($email, '@')) return $email;
        list($local, $domain) = explode('@', $email);
        if (strlen($local) <= 2) return $email;
        $first = substr($local, 0, 1);
        $last = substr($local, -1);
        $middle = str_repeat('*', strlen($local) - 2);
        return $first . $middle . $last . '@' . $domain;
    }

    public static function maskUsername($username)
    {
        if (!$username) return '-';
        if (strlen($username) <= 2) return $username;
        $first = substr($username, 0, 1);
        $last = substr($username, -1);
        $middle = str_repeat('*', strlen($username) - 2);
        return $first . $middle . $last;
    }
}