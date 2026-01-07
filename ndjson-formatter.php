<?php
/*
 /kunden/homepages/31/d249957217/htdocs/asiapi/product-feed/ndjson-formatter.php
 
 #python3 /usr/bin/python3 /kunden/homepages/31/d249957217/htdocs/asiapi/product-feed/catalog-upload.py /usr/bin/python3 /kunden/homepages/31/d249957217/htdocs/asiapi/product-feed/attentive-product-feed.ndjson --validateOnly true --apiKey bW9PeWh1WDVVd3U5YzBmNUtZeWcwclZQQjJkTXdiU2YxcVVw

  #02 13 * * 1,2,3,4,5 /usr/bin/python3 /kunden/homepages/31/d249957217/htdocs/asiapi/product-feed/catalog-upload.py /kunden/homepages/31/d249957217/htdocs/asiapi/product-feed/asi-attentive-product-feed.ndjson --validateOnly true --apiKey bW9PeWh1WDVVd3U5YzBmNUtZeWcwclZQQjJkTXdiU2YxcVVw
*/
$input = 'attentive-product-feed.csv';
$output = 'attentive-product-feed.ndjson';
$domain = 'https://www.americansignaturefurniture.com';

$fw = fopen($output, 'w') or die('Unable to open file');

function emptyFilter($var){
  return ($var !== NULL && $var !== FALSE && $var !== "");
}
print_r('writing');
if(($handle = fopen($input, "r")) !== FALSE) {
  fgets($handle);
  while (($data = fgetcsv($handle, 30000, ",")) !== FALSE){
    $patternUni = array("'é'", "'è'", "'ë'", "'ê'", "'É'", "'È'", "'Ë'", "'Ê'", "'á'", "'à'", "'ä'", "'â'", "'å'", "'Á'", "'À'", "'Ä'", "'Â'", "'Å'", "'ó'", "'ò'", "'ö'", "'ô'", "'Ó'", "'Ò'", "'Ö'", "'Ô'", "'í'", "'ì'", "'ï'", "'î'", "'Í'", "'Ì'", "'Ï'", "'Î'", "'ú'", "'ù'", "'ü'", "'û'", "'Ú'", "'Ù'", "'Ü'", "'Û'", "'ý'", "'ÿ'", "'Ý'", "'ø'", "'Ø'", "'œ'", "'Œ'", "'Æ'", "'ç'", "'Ç'","/\n/","/\r/","/\"/");
    $replaceUni = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 'I', 'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'y', 'y', 'Y', 'o', 'O', 'a', 'A', 'A', 'c', 'C','','',''); 
    $nameString = str_replace('"','in',$data[1]);
    $namer = str_replace("'","ft",$nameString);
    $descriptionString = $data[2];
    $description = preg_replace($patternUni, $replaceUni, $descriptionString);
    $linkString = preg_replace( "~\x{00a0}~siu", " ", $data[3]);
    $link = preg_replace($patternUni, $replaceUni, $link);
    $linker = "https://www.americansignaturefurniture.com" . $link;
    $pricesString = $data[4];
    $imagesString = $data[6];
    $categoriesString = $data[8];
    $attributesString = $data[9];
    $lastUpdated = "";
    $variantId = "";
    $variantName = "";
    if(!empty($data[16])){
      $variantId = $data[17];
      $variantName = $data[18];
      if($data[18]==""){
        $variantName = $data[1];
      }
      if(str_contains($data[20],"T")){
        $lastUpdated = str_replace("/","-",$data[20]);
      }else {
        $lastUpdated = str_replace("/","-",$data[20])."T00:00:00-05:00";
      }
    }else {
      $variantId = $data[0];
      $variantName = $data[17];
      if($data[17]==""){
        $variantName = $data[1];
      }
      if(str_contains($data[21],"T")){
        $lastUpdated = str_replace("/","-",$data[21]);
      }else {
        $lastUpdated = str_replace("/","-",$data[21])."T00:00:00-05:00";
      }
    }
    $variantsString = str_replace('}','',$data[17]);
    $createdString = str_replace("/","-",$data[19]);
    $pattern = "/=>/";
    $replace = ":";
    $prices = preg_replace($pattern, $replace, $pricesString);
    $images = str_replace("®", "", $imagesString);
    $pattern = "/=>/";
    $replace = ":";
    $variants = preg_replace($pattern, $replace, $variantsString);
    $arrPattern = "/,/";
    $arrReplace = '", "';
    $tagString = $data[10];
    $tagsArray = str_getcsv($tagString);
    $timestamp = new DateTime();
    $newcat = explode(",", $data[8]);
    $newcatjson = json_encode($newcat, true);
    $attributesString = $data[9];
      $attributes = preg_replace($pattern, $replace, $attributesString);
    $attributesobj = str_replace("}{","},{",$attributes);
    $attributesdecode = json_encode($attributesobj,true);
    $attributeTwo = "";
    if(!empty($data[10])){
      $tagger = $tagsArray;
    }
    else{$tagger = ["none"];}

    if(!empty($data[23])){
      $attributeTwo = $data[23];
    }else{$attributeTwo = "none";}
    $productsObj = (object) [
      'id' => $data[0],
      'name' => (string)$namer,
      'description' => $description,
      'brand' => $data[7],
      'link' => $domain.$data[3],
      'tags' => (array)$tagger,
      'categories' => (array)$categoriesString,
      'images' => [$img = (object)['src' => (string)$images]],
      'lastUpdated' => (string)$lastUpdated,
      'variants' => [(object)['id'=>$variantId,'name'=>$variantName,'prices' => [(object)['currencyCode'=>'USD','amount'=>$data[4]]],'availableForPurchase'=>true,'link'=>(string)$domain.'/product/'.$variantId,'lastUpdated' => (string)$lastUpdated]]
    ];
    // $timestamp->format(DateTime::ISO8601);
    $products = json_encode($productsObj, true, JSON_PRETTY_PRINT)." \n";
    fwrite($fw, str_replace("\/", "/", $products));
    echo "..." . "<br>";
  } 
}
print_r('closing');
fclose($fw);
fclose($handle);
?>