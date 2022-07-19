<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguageListController extends Controller
{
    /**
     * Get all language list in the world
     * Last update: 18 Aug 2019
     * 
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $lang = [
            "Abkhaz" => "Abkhaz",
            "Afar" => "Afar",
            "Afrikaans" => "Afrikaans",
            "Akan" => "Akan",
            "Albanian" => "Albanian",
            "Amharic" => "Amharic",
            "Arabic" => "Arabic",
            "Aragonese" => "Aragonese",
            "Armenian" => "Armenian",
            "Assamese" => "Assamese",
            "Avaric" => "Avaric",
            "Avestan" => "Avestan",
            "Aymara" => "Aymara",
            "Azerbaijani" => "Azerbaijani",
            "Bambara" => "Bambara",
            "Bashkir" => "Bashkir",
            "Basque" => "Basque",
            "Belarusian" => "Belarusian",
            "Bengali" => "Bengali",
            "Bihari" => "Bihari",
            "Bislama" => "Bislama",
            "Bosnian" => "Bosnian",
            "Breton" => "Breton",
            "Bulgarian" => "Bulgarian",
            "Burmese" => "Burmese",
            "Catalan; Valencian" => "Catalan; Valencian",
            "Chamorro" => "Chamorro",
            "Chechen" => "Chechen",
            "Chichewa; Chewa; Nyanja" => "Chichewa; Chewa; Nyanja",
            "Chinese" => "Chinese",
            "Chuvash" => "Chuvash",
            "Cornish" => "Cornish",
            "Corsican" => "Corsican",
            "Cree" => "Cree",
            "Croatian" => "Croatian",
            "Czech" => "Czech",
            "Danish" => "Danish",
            "Divehi; Dhivehi; Maldivian;" => "Divehi; Dhivehi; Maldivian;",
            "Dutch" => "Dutch",
            "English" => "English",
            "Esperanto" => "Esperanto",
            "Estonian" => "Estonian",
            "Ewe" => "Ewe",
            "Faroese" => "Faroese",
            "Fijian" => "Fijian",
            "Finnish" => "Finnish",
            "French" => "French",
            "Fula; Fulah; Pulaar; Pular" => "Fula; Fulah; Pulaar; Pular",
            "Galician" => "Galician",
            "Georgian" => "Georgian",
            "German" => "German",
            "Greek, Modern" => "Greek, Modern",
            "Guaraní" => "Guaraní",
            "Gujarati" => "Gujarati",
            "Haitian; Haitian Creole" => "Haitian; Haitian Creole",
            "Hausa" => "Hausa",
            "Hebrew (modern)" => "Hebrew (modern)",
            "Herero" => "Herero",
            "Hindi" => "Hindi",
            "Hiri Motu" => "Hiri Motu",
            "Hungarian" => "Hungarian",
            "Interlingua" => "Interlingua",
            "Indonesian" => "Indonesian",
            "Interlingue" => "Interlingue",
            "Irish" => "Irish",
            "Igbo" => "Igbo",
            "Inupiaq" => "Inupiaq",
            "Ido" => "Ido",
            "Icelandic" => "Icelandic",
            "Italian" => "Italian",
            "Inuktitut" => "Inuktitut",
            "Japanese" => "Japanese",
            "Javanese" => "Javanese",
            "Kalaallisut, Greenlandic" => "Kalaallisut, Greenlandic",
            "Kannada" => "Kannada",
            "Kanuri" => "Kanuri",
            "Kashmiri" => "Kashmiri",
            "Kazakh" => "Kazakh",
            "Khmer" => "Khmer",
            "Kikuyu, Gikuyu" => "Kikuyu, Gikuyu",
            "Kinyarwanda" => "Kinyarwanda",
            "Kirghiz, Kyrgyz" => "Kirghiz, Kyrgyz",
            "Komi" => "Komi",
            "Kongo" => "Kongo",
            "Korean" => "Korean",
            "Kurdish" => "Kurdish",
            "Kwanyama, Kuanyama" => "Kwanyama, Kuanyama",
            "Latin" => "Latin",
            "Luxembourgish, Letzeburgesch" => "Luxembourgish, Letzeburgesch",
            "Luganda" => "Luganda",
            "Limburgish, Limburgan, Limburger" => "Limburgish, Limburgan, Limburger",
            "Lingala" => "Lingala",
            "Lao" => "Lao",
            "Lithuanian" => "Lithuanian",
            "Luba-Katanga" => "Luba-Katanga",
            "Latvian" => "Latvian",
            "Manx" => "Manx",
            "Macedonian" => "Macedonian",
            "Malagasy" => "Malagasy",
            "Malay" => "Malay",
            "Malayalam" => "Malayalam",
            "Maltese" => "Maltese",
            "Māori" => "Māori",
            "Marathi (Marāṭhī)" => "Marathi (Marāṭhī)",
            "Marshallese" => "Marshallese",
            "Mongolian" => "Mongolian",
            "Nauru" => "Nauru",
            "Navajo, Navaho" => "Navajo, Navaho",
            "Norwegian Bokmål" => "Norwegian Bokmål",
            "North Ndebele" => "North Ndebele",
            "Nepali" => "Nepali",
            "Ndonga" => "Ndonga",
            "Norwegian Nynorsk" => "Norwegian Nynorsk",
            "Norwegian" => "Norwegian",
            "Nuosu" => "Nuosu",
            "South Ndebele" => "South Ndebele",
            "Occitan" => "Occitan",
            "Ojibwe, Ojibwa" => "Ojibwe, Ojibwa",
            "Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic" => "Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic",
            "Oromo" => "Oromo",
            "Oriya" => "Oriya",
            "Ossetian, Ossetic" => "Ossetian, Ossetic",
            "Panjabi, Punjabi" => "Panjabi, Punjabi",
            "Pāli" => "Pāli",
            "Persian" => "Persian",
            "Polish" => "Polish",
            "Pashto, Pushto" => "Pashto, Pushto",
            "Portuguese" => "Portuguese",
            "Quechua" => "Quechua",
            "Romansh" => "Romansh",
            "Kirundi" => "Kirundi",
            "Romanian, Moldavian, Moldovan" => "Romanian, Moldavian, Moldovan",
            "Russian" => "Russian",
            "Sanskrit (Saṁskṛta)" => "Sanskrit (Saṁskṛta)",
            "Sardinian" => "Sardinian",
            "Sindhi" => "Sindhi",
            "Northern Sami" => "Northern Sami",
            "Samoan" => "Samoan",
            "Sango" => "Sango",
            "Serbian" => "Serbian",
            "Scottish Gaelic; Gaelic" => "Scottish Gaelic; Gaelic",
            "Shona" => "Shona",
            "Sinhala, Sinhalese" => "Sinhala, Sinhalese",
            "Slovak" => "Slovak",
            "Slovene" => "Slovene",
            "Somali" => "Somali",
            "Southern Sotho" => "Southern Sotho",
            "Spanish; Castilian" => "Spanish; Castilian",
            "Sundanese" => "Sundanese",
            "Swahili" => "Swahili",
            "Swati" => "Swati",
            "Swedish" => "Swedish",
            "Tamil" => "Tamil",
            "Telugu" => "Telugu",
            "Tajik" => "Tajik",
            "Thai" => "Thai",
            "Tigrinya" => "Tigrinya",
            "Tibetan Standard, Tibetan, Central" => "Tibetan Standard, Tibetan, Central",
            "Turkmen" => "Turkmen",
            "Tagalog" => "Tagalog",
            "Tswana" => "Tswana",
            "Tonga (Tonga Islands)" => "Tonga (Tonga Islands)",
            "Turkish" => "Turkish",
            "Tsonga" => "Tsonga",
            "Tatar" => "Tatar",
            "Twi" => "Twi",
            "Tahitian" => "Tahitian",
            "Uighur, Uyghur" => "Uighur, Uyghur",
            "Ukrainian" => "Ukrainian",
            "Urdu" => "Urdu",
            "Uzbek" => "Uzbek",
            "Venda" => "Venda",
            "Vietnamese" => "Vietnamese",
            "Volapük" => "Volapük",
            "Walloon" => "Walloon",
            "Welsh" => "Welsh",
            "Wolof" => "Wolof",
            "Western Frisian" => "Western Frisian",
            "Xhosa" => "Xhosa",
            "Yiddish" => "Yiddish",
            "Yoruba" => "Yoruba",
            "Zhuang, Chuang" => "Zhuang, Chuang"
        ];

        $langArr = [];

        foreach ($lang as $key => $value) {
            array_push($langArr, $key);
        }

        return response()->json(['data' => $langArr], 200);
    }
}
