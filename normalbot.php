<?php
$sure_baslangici = microtime(true);
include 'baglan.php';
function curlKullan($url)
{
    $curl = curl_init();//curl tanımlandı
    curl_setopt($curl, CURLOPT_URL, $url);//curl e site girildi
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//curl ayarı yapıldı
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);//timeout ayarlandı
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");//curl ayarı yapıldı
    $curlData = curl_exec($curl);//curl çalıştırıldı
    curl_close($curl);//curl kapatıldı
    return $curlData;//veri döndürüldü
}

function ilanIcerigi($url){
    $data = curlKullan($url); //ilan çekildi
    $vals = array(); //ilana ait detayların ekleneceği dizi tanımlandı

    preg_match_all('/<h1>(.+?)<\/h1>/', $data, $rData , PREG_SET_ORDER);//ayıklama yapıldı
    $vals['baslik'] = trim($rData[0][1]);//ayıklanan veri html etiketleri olmadan $vals içine eklendi

	preg_match_all('@<strong>
                İlan Tarihi</strong>&nbsp;
            <span>(.*?)</span>@si', $data, $rData , PREG_SET_ORDER);
    $vals['tarih'] = trim($rData[0][1]);

    preg_match_all('@<div class="classifiedInfo ">

                    <h3>(.*?)<a class="emlak-endeksi@si', $data, $rData , PREG_SET_ORDER);
    $vals['fiyat'] = trim($rData[0][1]);

    preg_match_all('@<strong>m²</strong>&nbsp;
                <span class="">(.*?)</span>@si', $data, $rData , PREG_SET_ORDER);
    $vals['m2'] = trim($rData[0][1]);

    preg_match_all('@<strong>Oda Sayısı</strong>&nbsp;
                <span class="">(.*?)</span>@si', $data, $rData , PREG_SET_ORDER);
    $vals['oda_sayisi'] = trim($rData[0][1]);

    preg_match_all('@<strong>Bina Yaşı</strong>&nbsp;
                <span class="">(.*?)</span>@si', $data, $rData , PREG_SET_ORDER);
    $vals['bina_yasi'] = trim($rData[0][1]);

    preg_match_all('@<strong>Bulunduğu Kat</strong>&nbsp;
                <span class="">(.*?)</span>@si', $data, $rData , PREG_SET_ORDER);
    $vals['kat'] = trim($rData[0][1]);

    preg_match_all('@<strong>Isıtma</strong>&nbsp;
                <span class="">(.*?)</span>@si', $data, $rData , PREG_SET_ORDER);
    $vals['isitma'] = trim($rData[0][1]);

    preg_match_all('@data-lat="(.*?)"@si', $data, $rData , PREG_SET_ORDER);
    $vals['data-lat'] = trim($rData[0][1]);

     preg_match_all('@data-lon="(.*?)"@si', $data, $rData , PREG_SET_ORDER);
    $vals['data-lon'] = trim($rData[0][1]);

    preg_match_all('@<strong>Kimden</strong>&nbsp;
                <span class="">(.*?)</span>@si', $data, $rData , PREG_SET_ORDER);
    $vals['kimden'] = trim($rData[0][1]);


    //cephe eklemesi başlangıç

    preg_match_all('@<li class="selected">(.*?)</li>@si', $data, $rData , PREG_SET_ORDER);
    $vals['cephe'] = trim($rData[0][1]);
    

    // cephe bitiş...

    // eklendi..

    if (empty($vals['baslik'])|| empty($vals['tarih']) || empty($vals['fiyat']) || empty($vals['m2']) || empty($vals['oda_sayisi']) || empty($vals['bina_yasi']) || empty($vals['kat']) || empty($vals['isitma'])|| empty($vals['data-lat'])|| empty($vals['data-lon']) || empty($vals['kimden']) || empty($vals['cephe']) ) {
    	echo $url.'<br>';
    }

    return $vals;
}


$site = curlKullan("https://www.sahibinden.com/satilik/aksaray-merkez");

preg_match_all('/aramanızda <span>(.+?) ilan<\/span> bulundu./', $site, $veri_sayisi , PREG_SET_ORDER);
$sayfaSayisi = ceil($veri_sayisi[0][1]/20);
$sayfaIcerik = '';
for($i=0;$i<$sayfaSayisi;$i++){
	$sayfaIcerik .= curlKullan("https://www.sahibinden.com/satilik/aksaray-merkez?pagingOffset=" . $i*20);
}
$dizi = array();
	preg_match_all('@href="/ilan/(.*?)">@si', $sayfaIcerik, $veri_derece1 , PREG_SET_ORDER);
$veri_derece1[-1][1] = null;
	for ($i=0;$i<count($veri_derece1);$i++) {
		if($veri_derece1[$i][1] != $veri_derece1[$i-1][1]){
			$icerik = ilanIcerigi('https://www.sahibinden.com/ilan/' . $veri_derece1[$i][1]);
			$db->query("INSERT INTO 
				sahibinden(`baslik`,`tarih`,`fiyat`,`m2`,`oda_sayisi`,`bina_yasi`,`kat`,`isitma`,`data-lat`,`data-lon`,`url`,`kimden`,`cephe`)
				VALUES(
				'" . $icerik["baslik"] . "',
				'" . $icerik["tarih"] . "',
				'" . $icerik["fiyat"] . "',
				'" . $icerik["m2"] . "',
				'" . $icerik["oda_sayisi"] . "',
				'" . $icerik["bina_yasi"] . "',
				'" . $icerik["kat"] . "',
				'" . $icerik["isitma"] . "',
				'" . $icerik["data-lat"] . "',
				'" . $icerik["data-lon"] . "',
				'" . 'https://www.sahibinden.com/ilan/' . $veri_derece1[$i][1] . "',
                '" . $icerik["kimden"] . "',
                '" . $icerik["cephe"] . "'
			)");


		}
	}

    
echo 'succses<br>';
$sure_bitimi = microtime(true);
$sure = $sure_bitimi - $sure_baslangici;

echo "Bekleme süresi: $sure saniye.\n";

?>


