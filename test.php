<?php 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");


//array of data to POST
$request_contents = array();
//array of URLs

//array of cURL handles
$chs = array();

//set the urls
$urls = [
"https://en.wikipedia.org/wiki/101_California_Street_shooting",
"https://en.wikipedia.org/wiki/1965_Highway_101_sniper_attack",
"https://en.wikipedia.org/wiki/2001_Greyhound_bus_attack",
"https://en.wikipedia.org/wiki/2008_Skagit_County_shootings",
"https://en.wikipedia.org/wiki/2009_Collier_Township_shooting",
"https://en.wikipedia.org/wiki/2011_Grand_Rapids_mass_murder",
"https://en.wikipedia.org/wiki/2011_Seal_Beach_shooting",
"https://en.wikipedia.org/wiki/2013_Hialeah_shooting",
"https://en.wikipedia.org/wiki/2014_Isla_Vista_killings",
"https://en.wikipedia.org/wiki/2016_Kalamazoo_shootings",
"https://en.wikipedia.org/wiki/2018_Scottsdale_shootings",
"https://en.wikipedia.org/wiki/2019_Dayton_shooting",
"https://en.wikipedia.org/wiki/2021_Atlanta_spa_shootings",
"https://en.wikipedia.org/wiki/2021_Boulder_shooting",
"https://en.wikipedia.org/wiki/Binghamton_shooting",
"https://en.wikipedia.org/wiki/Capitol_Hill_massacre",
"https://en.wikipedia.org/wiki/Caril_Ann_Fugate",
"https://en.wikipedia.org/wiki/Carl_Robert_Brown",
"https://en.wikipedia.org/wiki/Carson_City_IHOP_shooting",
"https://en.wikipedia.org/wiki/Carthage_nursing_home_shooting",
"https://en.wikipedia.org/wiki/Chai_Vang",
"https://en.wikipedia.org/wiki/Charles_Starkweather",
"https://en.wikipedia.org/wiki/Cleophus_Cooksey_Jr.",
"https://en.wikipedia.org/wiki/Craig_Alaska#1982_massacre",
"https://en.wikipedia.org/wiki/Crandon_shooting",
"https://en.wikipedia.org/wiki/Daingerfield_church_shooting",
"https://en.wikipedia.org/wiki/Eliseo_Moreno",
"https://en.wikipedia.org/wiki/Fairchild_Air_Force_Base#1994_shooting_incident",
"https://en.wikipedia.org/wiki/Fort_Lauderdale_airport_shooting",
"https://en.wikipedia.org/wiki/Geneva_County_shootings",
"https://en.wikipedia.org/wiki/Hackettstown_New_Jersey#20th_Century",
"https://en.wikipedia.org/wiki/Herbert_Mullin",
"https://en.wikipedia.org/wiki/Howard_Unruh",
"https://en.wikipedia.org/wiki/Jacksonville_Landing_shooting",
"https://en.wikipedia.org/wiki/James_Edward_Pough",
"https://en.wikipedia.org/wiki/James_Holmes_(mass_murderer)",
"https://en.wikipedia.org/wiki/Jared_Lee_Loughner",
"https://en.wikipedia.org/wiki/Jim_Jumper_massacre",
"https://en.wikipedia.org/wiki/Joe_Pullen",
"https://en.wikipedia.org/wiki/John_Allen_Muhammad",
"https://en.wikipedia.org/wiki/Kirkwood_City_Council_shooting",
"https://en.wikipedia.org/wiki/Larry_Gene_Ashbrook",
"https://en.wikipedia.org/wiki/Lee_Boyd_Malvo",
"https://en.wikipedia.org/wiki/Luby%27s_shooting",
"https://en.wikipedia.org/wiki/Luther_Casteel",
"https://en.wikipedia.org/wiki/Lynwood_Drake",
"https://en.wikipedia.org/wiki/McCarthy_Alaska#1983_shooting",
"https://en.wikipedia.org/wiki/Michael_Silka",
"https://en.wikipedia.org/wiki/Midland%E2%80%93Odessa_shooting",
"https://en.wikipedia.org/wiki/Nicholas_Troy_Sheley",
"https://en.wikipedia.org/wiki/North_Hills_(Raleigh)#1972_shooting",
"https://en.wikipedia.org/wiki/Oregon_Museum_Tavern_shooting",
"https://en.wikipedia.org/wiki/Pacific_Air_Lines_Flight_773",
"https://en.wikipedia.org/wiki/Pacific_Southwest_Airlines_Flight_1771",
"https://en.wikipedia.org/wiki/Palm_Bay_Florida#1987_shooting",
"https://en.wikipedia.org/wiki/Rancho_Tehama_Reserve_shootings",
"https://en.wikipedia.org/wiki/Russell_Lee_Smith",
"https://en.wikipedia.org/wiki/San_Ysidro_McDonald%27s_massacre",
"https://en.wikipedia.org/wiki/Stephen_Paddock",
"https://en.wikipedia.org/wiki/Sutherland_Springs_church_shooting",
"https://en.wikipedia.org/wiki/Terry_Ratzmann",
"https://en.wikipedia.org/wiki/Thousand_Oaks_shooting",
"https://en.wikipedia.org/wiki/Utah_prisoner_of_war_massacre",
"https://en.wikipedia.org/wiki/Westroads_Mall_shooting",
"https://en.wikipedia.org/wiki/Will_Reynolds",
"https://en.wikipedia.org/wiki/William_Ray_Bonner",
"https://en.wikipedia.org/wiki/Winfield_Kansas#Historical_event",
"https://en.wikipedia.org/wiki/Young_Brothers_massacre
",
"https://en.wikipedia.org/wiki/English_Wikipedia",
"https://en.wikipedia.org/wiki/French_Wikipedia",
"https://en.wikipedia.org/wiki/German_Wikipedia",
"https://en.wikipedia.org/wiki/Japanese_Wikipedia",
"https://en.wikipedia.org/wiki/Spanish_Wikipedia",
"https://en.wikipedia.org/wiki/Russian_Wikipedia",
"https://en.wikipedia.org/wiki/Portuguese_Wikipedia",
"https://en.wikipedia.org/wiki/Chinese_Wikipedia",
"https://en.wikipedia.org/wiki/Italian_Wikipedia",
"https://en.wikipedia.org/wiki/Arabic_Wikipedia",
"https://en.wikipedia.org/wiki/Persian_Wikipedia",
"https://en.wikipedia.org/wiki/Polish_Wikipedia",
"https://en.wikipedia.org/wiki/Dutch_Wikipedia",
"https://en.wikipedia.org/wiki/Ukrainian_Wikipedia",
"https://en.wikipedia.org/wiki/Indonesian_Wikipedia",
"https://en.wikipedia.org/wiki/Turkish_Wikipedia",
"https://en.wikipedia.org/wiki/Hebrew_Wikipedia",
"https://en.wikipedia.org/wiki/Vietnamese_Wikipedia",
"https://en.wikipedia.org/wiki/Czech_Wikipedia",
"https://en.wikipedia.org/wiki/Swedish_Wikipedia",
"https://en.wikipedia.org/wiki/Korean_Wikipedia",
"https://en.wikipedia.org/wiki/Finnish_Wikipedia",
"https://en.wikipedia.org/wiki/Hungarian_Wikipedia",
"https://en.wikipedia.org/wiki/Catalan_Wikipedia",
"https://en.wikipedia.org/wiki/Norwegian_Wikipedia",
"https://en.wikipedia.org/wiki/Hindi_Wikipedia",
"https://en.wikipedia.org/wiki/Bengali_Wikipedia",
"https://en.wikipedia.org/wiki/Simple_English_Wikipedia",
"https://en.wikipedia.org/wiki/Greek_Wikipedia",
"https://en.wikipedia.org/wiki/Thai_Wikipedia",
"https://en.wikipedia.org/wiki/Serbian_Wikipedia",
"https://en.wikipedia.org/wiki/Romanian_Wikipedia",
"https://en.wikipedia.org/wiki/Azerbaijani_Wikipedia",
"https://en.wikipedia.org/wiki/Danish_Wikipedia",
"https://en.wikipedia.org/wiki/Bulgarian_Wikipedia",
"https://en.wikipedia.org/wiki/Basque_Wikipedia",
"https://en.wikipedia.org/wiki/Estonian_Wikipedia",
"https://en.wikipedia.org/wiki/Slovak_Wikipedia",
"https://en.wikipedia.org/wiki/Malay_Wikipedia",
"https://en.wikipedia.org/wiki/Croatian_Wikipedia",
"https://en.wikipedia.org/wiki/Armenian_Wikipedia",
"https://en.wikipedia.org/wiki/Slovene_Wikipedia",
"https://en.wikipedia.org/wiki/Lithuanian_Wikipedia",
"https://en.wikipedia.org/wiki/Galician_Wikipedia",
"https://en.wikipedia.org/wiki/Esperanto_Wikipedia",
"https://en.wikipedia.org/wiki/Cantonese_Wikipedia",
"https://en.wikipedia.org/wiki/Latvian_Wikipedia",
"https://en.wikipedia.org/wiki/Macedonian_Wikipedia",
"https://en.wikipedia.org/wiki/Malayalam_Wikipedia",
"https://en.wikipedia.org/wiki/Tamil_Wikipedia",
"https://en.wikipedia.org/wiki/Georgian_Wikipedia",
"https://en.wikipedia.org/wiki/Kazakh_Wikipedia",
"https://en.wikipedia.org/wiki/Belarusian_Wikipedia",
"https://en.wikipedia.org/wiki/Uzbek_Wikipedia",
"https://en.wikipedia.org/wiki/Albanian_Wikipedia",
"https://en.wikipedia.org/wiki/Urdu_Wikipedia",
"https://en.wikipedia.org/wiki/Marathi_Wikipedia",
"https://en.wikipedia.org/wiki/Kannada_Wikipedia",
"https://en.wikipedia.org/wiki/Egyptian_Arabic_Wikipedia",
"https://en.wikipedia.org/wiki/Serbo-Croatian_Wikipedia",
"https://en.wikipedia.org/wiki/Telugu_Wikipedia",
"https://en.wikipedia.org/wiki/Bosnian_Wikipedia",
"https://en.wikipedia.org/wiki/Cebuano_Wikipedia",
"https://en.wikipedia.org/wiki/Afrikaans_Wikipedia",
"https://en.wikipedia.org/wiki/Latin_Wikipedia",
"https://en.wikipedia.org/wiki/Norwegian_Wikipedia",
"https://en.wikipedia.org/wiki/Burmese_Wikipedia",
"https://en.wikipedia.org/wiki/Sorani_Kurdish_Wikipedia",
"https://en.wikipedia.org/wiki/Swahili_Wikipedia",
"https://en.wikipedia.org/wiki/Tagalog_Wikipedia",
"https://en.wikipedia.org/wiki/Punjabi_Wikipedia_(Eastern)",
"https://en.wikipedia.org/wiki/Asturian_Wikipedia",
"https://en.wikipedia.org/wiki/Icelandic_Wikipedia",
"https://en.wikipedia.org/wiki/Welsh_Wikipedia",
"https://en.wikipedia.org/wiki/South_Azerbaijani_Wikipedia",
"https://en.wikipedia.org/wiki/Belarusian_Wikipedia",
"https://en.wikipedia.org/wiki/Mongolian_Wikipedia",
"https://en.wikipedia.org/wiki/Javanese_Wikipedia",
"https://en.wikipedia.org/wiki/Kyrgyz_Wikipedia",
"https://en.wikipedia.org/wiki/Breton_Wikipedia",
"https://en.wikipedia.org/wiki/Alemannic_Wikipedia",
"https://en.wikipedia.org/wiki/Min_Nan_Wikipedia",
"https://en.wikipedia.org/wiki/Occitan_Wikipedia",
"https://en.wikipedia.org/wiki/Scots_Wikipedia",
"https://en.wikipedia.org/wiki/Tatar_Wikipedia",
"https://en.wikipedia.org/wiki/Aragonese_Wikipedia",
"https://en.wikipedia.org/wiki/Assamese_Wikipedia",
"https://en.wikipedia.org/wiki/Irish_Wikipedia",
"https://en.wikipedia.org/wiki/Nepali_Wikipedia",
"https://en.wikipedia.org/wiki/Waray_Wikipedia",
"https://en.wikipedia.org/wiki/Kurdish_Wikipedia",
"https://en.wikipedia.org/wiki/Gujarati_Wikipedia",
"https://en.wikipedia.org/wiki/West_Frisian_Wikipedia",
"https://en.wikipedia.org/wiki/Sundanese_Wikipedia",
"https://en.wikipedia.org/wiki/Luxembourgish_Wikipedia",
"https://en.wikipedia.org/wiki/Bavarian_Wikipedia",
"https://en.wikipedia.org/wiki/Yiddish_Wikipedia",
"https://en.wikipedia.org/wiki/Chuvash_Wikipedia",
"https://en.wikipedia.org/wiki/Punjabi_Wikipedia_(Western)",
"https://en.wikipedia.org/wiki/Minangkabau_Wikipedia",
"https://en.wikipedia.org/wiki/Oriya_Wikipedia",
"https://en.wikipedia.org/wiki/Ido_Wikipedia",
"https://en.wikipedia.org/wiki/Pashto_Wikipedia",
"https://en.wikipedia.org/wiki/Bhojpuri_Wikipedia",
"https://en.wikipedia.org/wiki/Sicilian_Wikipedia",
"https://en.wikipedia.org/wiki/Sanskrit_Wikipedia",
"https://en.wikipedia.org/wiki/Scottish_Gaelic_Wikipedia",
"https://en.wikipedia.org/wiki/Silesian_Wikipedia",
"https://en.wikipedia.org/wiki/Rusyn_Wikipedia",
"https://en.wikipedia.org/wiki/Western_Armenian_Wikipedia",
"https://en.wikipedia.org/wiki/Santali_Wikipedia",
"https://en.wikipedia.org/wiki/Ossetian_Wikipedia",
"https://en.wikipedia.org/wiki/Uyghur_Wikipedia",
"https://en.wikipedia.org/wiki/Volap%C3%BCk_Wikipedia",
"https://en.wikipedia.org/wiki/Zulu_Wikipedia",
"https://en.wikipedia.org/wiki/Konkani_Wikipedia",
"https://en.wikipedia.org/wiki/Kotava_Wikipedia",
"https://en.wikipedia.org/wiki/Dutch_Low_Saxon_Wikipedia",
"https://en.wikipedia.org/wiki/Ladino_Wikipedia",
"https://en.wikipedia.org/wiki/Northern_Sami_Wikipedia",
"https://en.wikipedia.org/wiki/Crimean_Tatar_Wikipedia",
"https://en.wikipedia.org/wiki/Samogitian_Wikipedia",
"https://en.wikipedia.org/wiki/Sindhi_Wikipedia",
"https://en.wikipedia.org/wiki/Bishnupriya_Manipuri_Wikipedia",
"https://en.wikipedia.org/wiki/Extremaduran_Wikipedia",
"https://en.wikipedia.org/wiki/Kapampangan_Wikipedia",
"https://en.wikipedia.org/wiki/Maithili_Wikipedia",
"https://en.wikipedia.org/wiki/Newar_Wikipedia",
"https://en.wikipedia.org/wiki/Tulu_Wikipedia",
"https://en.wikipedia.org/wiki/Guarani_Wikipedia",
"https://en.wikipedia.org/wiki/Aromanian_Wikipedia",
"https://en.wikipedia.org/wiki/Wolof_Wikipedia",
"https://en.wikipedia.org/wiki/Kabyle_Wikipedia",
"https://en.wikipedia.org/wiki/Ripuarian_Wikipedia",
"https://en.wikipedia.org/wiki/Shan_Wikipedia",
"https://en.wikipedia.org/wiki/Xhosa_Wikipedia",
"https://en.wikipedia.org/wiki/Bambara_Wikipedia",
"https://en.wikipedia.org/wiki/Buginese_Wikipedia",
"https://en.wikipedia.org/wiki/Mon_Wikipedia",
"https://en.wikipedia.org/wiki/Pangasinan_Wikipedia",
"https://en.wikipedia.org/wiki/Saraiki_Wikipedia",
"https://en.wikipedia.org/wiki/English_language",
"https://en.wikipedia.org/wiki/French_language",
"https://en.wikipedia.org/wiki/German_language",
"https://en.wikipedia.org/wiki/Japanese_language",
"https://en.wikipedia.org/wiki/Spanish_language",
"https://en.wikipedia.org/wiki/Russian_language",
"https://en.wikipedia.org/wiki/Portuguese_language",
"https://en.wikipedia.org/wiki/Chinese_language",
"https://en.wikipedia.org/wiki/Written_vernacular_Chinese",
"https://en.wikipedia.org/wiki/Mandarin_Chinese",
"https://en.wikipedia.org/wiki/Italian_language",
"https://en.wikipedia.org/wiki/Arabic",
"https://en.wikipedia.org/wiki/Persian_language",
"https://en.wikipedia.org/wiki/Polish_language",
"https://en.wikipedia.org/wiki/Dutch_language",
"https://en.wikipedia.org/wiki/Ukrainian_language",
"https://en.wikipedia.org/wiki/Indonesian_language",
"https://en.wikipedia.org/wiki/Turkish_language",
"https://en.wikipedia.org/wiki/Hebrew_language",
"https://en.wikipedia.org/wiki/Vietnamese_language",
"https://en.wikipedia.org/wiki/Czech_language",
"https://en.wikipedia.org/wiki/Swedish_language",
"https://en.wikipedia.org/wiki/Korean_language",
"https://en.wikipedia.org/wiki/Finnish_language",
"https://en.wikipedia.org/wiki/Hungarian_language",
"https://en.wikipedia.org/wiki/Catalan_language",
"https://en.wikipedia.org/wiki/Norwegian_language",
"https://en.wikipedia.org/wiki/Bokm%C3%A5l",
"https://en.wikipedia.org/wiki/Hindi",
"https://en.wikipedia.org/wiki/Bengali_language",
"https://en.wikipedia.org/wiki/Basic_English",
"https://en.wikipedia.org/wiki/Greek_language",
"https://en.wikipedia.org/wiki/Thai_language",
"https://en.wikipedia.org/wiki/Serbian_language",
"https://en.wikipedia.org/wiki/Romanian_language",
"https://en.wikipedia.org/wiki/Azerbaijani_language",
"https://en.wikipedia.org/wiki/Danish_language",
"https://en.wikipedia.org/wiki/Bulgarian_language",
"https://en.wikipedia.org/wiki/Basque_language",
"https://en.wikipedia.org/wiki/Estonian_language",
"https://en.wikipedia.org/wiki/Slovak_language",
"https://en.wikipedia.org/wiki/Malay_language",
"https://en.wikipedia.org/wiki/Croatian_language",
"https://en.wikipedia.org/wiki/Armenian_language",
"https://en.wikipedia.org/wiki/Slovene_language",
"https://en.wikipedia.org/wiki/Lithuanian_language",
"https://en.wikipedia.org/wiki/Galician_language",
"https://en.wikipedia.org/wiki/Esperanto",
"https://en.wikipedia.org/wiki/Sango_language",
"https://en.wikipedia.org/wiki/Inupiaq_language",
"https://en.wikipedia.org/wiki/Kongo_language",
"wrongUrl1",
"http://Wrongurl2.com"];

//var_dump($urls);

//create the array of cURL handles and add to a multi_curl
$mh = curl_multi_init();
foreach ($urls as $key => $url) {
    $chs[$key] = curl_init($url);
    curl_setopt($chs[$key], CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($chs[$key], CURLOPT_FAILONERROR, true);
    curl_multi_add_handle($mh, $chs[$key]);
}

//running the requests
$running = null;
do {
  $status = curl_multi_exec($mh, $running);
  if ($running) {
      // Wait a short time for more activity
      curl_multi_select($mh);
  }
} while ($running && $status == CURLM_OK);

if ($status != CURLM_OK) {
    // Display error message
    echo "ERROR!\n " . curl_multi_strerror($status);
    die();
}

//getting the responses
foreach(array_keys($chs) as $key){
    $error = curl_error($chs[$key]);
    $header = curl_getinfo($chs[$key], CURLINFO_HTTP_CODE);
    $time = curl_getinfo($chs[$key], CURLINFO_TOTAL_TIME);
    $response = curl_multi_getcontent($chs[$key]);  // get results
    if ($header != 200) {
      echo var_dump($error);
    }
    else {
      echo "<br>" . "The request to $urls[$key] returned  in $time seconds as $header" . "<br>";
    }
    //var_dump($response);
    curl_multi_remove_handle($mh, $chs[$key]);
}


// close current handler
curl_multi_close($mh);

?>