<?php

if (! function_exists('text')) {
    function countriesList(): array {
        return  [
            "RU" => 'Russia',
            "MN" => 'Mongolia',
            "NZ" => 'New Zealand',
            "AU" => 'Australia',
            "JP" => 'Japan',
            "UM" => 'United States of America',
            "AF" => 'Afghanistan',
            "AL" => 'Albania',
            "AD" => 'Andorra',
            "AG" => 'Antigua and Barbuda',
            "AM" => 'Armenia',
            "AO" => 'Angola',
            "AR" => 'Argentina',
            "AT" => 'Austria',
            "AZ" => 'Azerbaijan',
            "BA" => 'Bosnia and Herzegovina',
            "BB" => 'Barbados',
            "BD" => 'Bangladesh',
            "BE" => 'Belgium',
            "BF" => 'Burkina Faso',
            "BG" => 'Bulgaria',
            "BH" => 'Bahrain',
            "BJ" => 'Benin',
            "BN" => 'Brunei',
            "BO" => 'Bolivia',
            "BT" => 'Bhutan',
            "BY" => 'Belarus',
            "CD" => 'Congo',
            "CA" => 'Canada',
            "CF" => 'Central African Republic',
            "CI" => 'Cote d\'Ivoire',
            "CL" => 'Chile',
            "CM" => 'Cameroon',
            "CN" => 'China',
            "CO" => 'Colombia',
            "CU" => 'Cuba',
            "CV" => 'Cabo Verde',
            "CY" => 'Cyprus',
            "DJ" => 'Djibouti',
            "DK" => 'Denmark',
            "DM" => 'Dominica',
            "DO" => 'Dominican Republic',
            "EC" => 'Ecuador',
            "EE" => 'Estonia',
            "ER" => 'Eritrea',
            "ET" => 'Ethiopia',
            "FI" => 'Finland',
            "FJ" => 'Fiji',
            "FR" => 'France',
            "GA" => 'Gabon',
            "GD" => 'Grenada',
            "GE" => 'Georgia',
            "GH" => 'Ghana',
            "HN" => 'Honduras',
            "HT" => 'Haiti',
            "HU" => 'Hungary',
            "ID" => 'Indonesia',
            "IE" => 'Ireland',
            "IL" => 'Israel',
            "IN" => 'India',
            "IQ" => 'Iraq',
            "IR" => 'Iran',
            "IS" => 'Iceland',
            "IT" => 'Italy',
            "JM" => 'Jamaica',
            "JO" => 'Jordan',
            "KE" => 'Kenya',
            "KG" => 'Kyrgyzstan',
            "KI" => 'Kiribati',
            "KW" => 'Kuwait',
            "KZ" => 'Kazakhstan',
            "LA" => 'Laos',
            "LB" => 'Lebanons',
            "LI" => 'Liechtenstein',
            "LR" => 'Liberia',
            "LS" => 'Lesotho',
            "LT" => 'Lithuania',
            "LU" => 'Luxembourg',
            "LV" => 'Latvia',
            "LY" => 'Libya',
            "MA" => 'Morocco',
            "MC" => 'Monaco',
            "MD" => 'Moldova',
            "ME" => 'Montenegro',
            "MG" => 'Madagascar',
            "MH" => 'Marshall Islands',
            "MK" => 'Macedonia (FYROM)',
            "ML" => 'Mali',
            "MM" => 'Myanmar (formerly Burma)',
            "MR" => 'Mauritania',
            "MT" => 'Malta',
            "MV" => 'Maldives',
            "MW" => 'Malawi',
            "MX" => 'Mexico',
            "MZ" => 'Mozambique',
            "NA" => 'Namibia',
            "NG" => 'Nigeria',
            "NO" => 'Norway',
            "NP" => 'Nepal',
            "NR" => 'Nauru',
            "OM" => 'Oman',
            "PA" => 'Panama',
            "PF" => 'Paraguay',
            "PG" => 'Papua New Guinea',
            "PH" => 'Philippines',
            "PK" => 'Pakistan',
            "PL" => 'Poland',
            "QA" => 'Qatar',
            "RO" => 'Romania',
            "RW" => 'Rwanda',
            "SA" => 'SInterstate Arabia',
            "SB" => 'Solomon Islands',
            "SC" => 'Seychelles',
            "SD" => 'Sudan',
            "SE" => 'Sweden',
            "SG" => 'Singapore',
            "TG" => 'Togo',
            "TH" => 'Thailand',
            "TJ" => 'Tajikistan',
            "TL" => 'Timor-Leste',
            "TM" => 'Turkmenistan',
            "TN" => 'Tunisia',
            "TZ" => 'Tanzania',
            "TO" => 'Tonga',
            "TR" => 'Turkey',
            "TT" => 'Trinidad and Tobago',
            "TW" => 'Taiwan',
            "UA" => 'Ukraine',
            "UG" => 'Uganda',
            "UY" => 'Uruguay',
            "UZ" => 'Uzbekistan',
            "VA" => 'Vatican City (Holy See)',
            "VE" => 'Venezuela',
            "VN" => 'Vietnam',
            "VU" => 'Vanuatu',
            "YE" => 'Yemen',
            "ZM" => 'Zambia',
            "ZW" => 'Zimbabwe',
            "KR" => 'Korea',
            "MY" => 'Malaysia',
            "GB" => 'England',
        ];
    }
}

if (!function_exists('findCountryByCode')) {
    function findCountryByCode(string $countryCode = ''): string
    {
        $countries = countriesList();
        foreach ($countries as $code => $country) {
            if ($code === $countryCode) {
                return $country;
            }
        }
        return "";
    }
}

if (!function_exists('findCodeByCountry')) {
    function findCodeByCountry(string $countryName = ''): string
    {
        $countries = countriesList();
        foreach($countries as $code => $country) {
            if ($country === $countryName) {
                return $code;
            }
        }
        return "";
    }
}

if (!function_exists('getCountriesForJson')) {
    function getCountriesForJson(): array
    {
        $countries = [];
        $countriesList = countriesList();
        foreach($countriesList as $key => $value) {
            $countries[] = ['code' => $key, 'country_name' => $value];
        }
        return $countries;
    }
}

if (! function_exists('arrayToJsonFormat')) {
    function arrayToJsonFormat(array $array): array
    {
        $jsonFormatted = [];
        foreach($array as $key => $value) {
            $jsonFormatted[] = [
              'name' => $key,
              'value' => $value,
            ];
        }
        return $jsonFormatted;
    }
}
