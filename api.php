<?php
class CTrackingInfo
{

    public function __construct($trackingId)
    {
        $this->endpoint = "https://track.amazon.it/api/tracker/" . $trackingId;
        $this->stringsEndpoint = "https://track.amazon.it/getLocalizedStrings";
        $this->ckfile = tempnam("/tmp", "cookie");
        $this->useragent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2';
    }

    public function getTrackingData()
    {
        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->ckfile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->ckfile);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_REFERER, $this->endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        $json = curl_exec($ch);
        $orderData = json_decode($json);
       // echo $eventHistory;
        if (curl_errno($ch)) {
            echo '[cURL ERROR]: ' . curl_error($ch);
        }
        curl_close($ch);

        return $eventHistory = json_decode($orderData->eventHistory);
    }

    public function getLocalizedStrings($eventHistory) {

        $stringsToLocalise = [];

        print("Stringhe da tradurre:\n");
        $i = 0;
        foreach($eventHistory as $key){
            for(null; $i < count((array)$key); $i++) {
                if ($key[$i]->statusSummary->localisedStringId != null) {
                    array_push($stringsToLocalise, $key[$i]->statusSummary->localisedStringId);
                }
            }
        }
        //print_r(json_encode(array('localizationKeys' => $stringsToLocalise)));
        print_r($stringsToLocalise);
        print("proviamo a tradurle...\n");
        $ch = curl_init($this->stringsEndpoint);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->ckfile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->ckfile);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('localizationKeys' => $stringsToLocalise)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Origin: https://track.amazon.it'));
        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Host: track.amazon.it'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_REFERER, $this->endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        $json = curl_exec($ch);
        $result = json_decode($json);
        if (curl_errno($ch)) {
            echo '[cURL ERROR]: ' . curl_error($ch);
        }
        curl_close($ch);
        print($json);
    }


}

$order1 = new CTrackingInfo("F297C70575548");
$order2 = new CTrackingInfo("914D60076496F");
$order3 = new CTrackingInfo("IT2222629746");
print("=============== ORDINE 1 ================\n");
print(json_encode($order1->getTrackingData()));
print("\n");
print("=============== ORDINE 2 ================\n");
print(json_encode($order2->getTrackingData()));
print("\n");
print("=============== ORDINE 3 ================\n");
print(json_encode($order3->getTrackingData()));

print("Ora proviamo a trovare le stringhe da tradurre...\n");
$order1->getLocalizedStrings($order1->getTrackingData());
$order2->getLocalizedStrings($order2->getTrackingData());
$order3->getLocalizedStrings($order3->getTrackingData());