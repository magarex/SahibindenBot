<?php


function siteConnect($site)
{
    $queryText = '';
    $ch = curl_init();
    $hc = "YahooSeeker-Testing/v3.9 (compatible; Mozilla 4.0; MSIE 5.5; Yahoo! Search - Web Search)";
    curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com');
    curl_setopt($ch, CURLOPT_URL, $site);
    curl_setopt($ch, CURLOPT_USERAGENT, $hc);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $site = curl_exec($ch);
    curl_close($ch);



    // Veriyi parçalama işlemi
    preg_match_all('@href="/ilan/(.*?)>@si', $site, $veri_derece1);
    

        $say= count($veri_derece1[0]);

        echo $say;


        for ($i=0; $i <40 ; $i++) { 
            
        print_r($veri_derece1[0][$i]);

            
            }

        

        

        die();
	

    //İndisleri for'a sokuyoruz.

    $say = count($veri_derece1[0]);


    if ($say > 0) {

        for ($i = 0; $i < $say; $i++) {
            $urun = strip_tags($veri_derece1[0][$i]);
            $fiyat = strip_tags($veri_derece2[0][$i]);
            $link = $veri_derece3[0][$i];

            $exp= explode('data-original="//', $link);
            $replace=str_replace('"',"",$exp);


            $queryText = $queryText . "insert into defacto(urun,fiyat,link) values('$urun','$fiyat','$replace[1]');";
        }
    }
    return $queryText;
}


$giris = siteConnect('https://www.sahibinden.com/satilik/aksaray-merkez');

echo $giris;

//veri tabanı insert'i buraya yazıyoruz.
$db->query($giris); 





?>




