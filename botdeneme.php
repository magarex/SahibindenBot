<?php




$site = file_get_contents("https://www.sahibinden.com/ilan/emlak-konut-satilik-nakkas-mah-4-plus1-170-m2-site-icerisinde-daire-533200298/detay");


    preg_match_all('@<strong>
                İlan Tarihi</strong>&nbsp;
            <span>(.*?)</span>@si', $site, $veri_derece1);



    print_r($veri_derece1);


?>