<?php
include "common.php";
$l = connectToDB();

for ($i=1; $i<100; $i++) {
    echo getArticle();
}


//////////////////////////////////////
function getArticle() {
$strings = file("strings.txt");

$titles = array_filter($strings, $f=fn($i)=>mb_strlen($i) < 50 && mb_strlen($i) > 30);
shuffle($titles);
$title = preg_replace("/\.|\n$/u","",current($titles));

$furl = translit($title);

$img = "<img src=\"/images/dummy.png\" width=\"500\" height=\"200\" alt=\"Dummy\"/><p>Рис. №. $title</p>"; 

$keywords = ['амфибрахий','эвокация комет','компенсация синеклиза','синеклиз','пыльца времени','речевой акт','аномальные геохимические ряды','проекция','два хвоста','деструктивный дискурс','туффит','ямбохорей','вторичный авгит','субъективное восприятие','успокоитель качки','компенсация чего','космический мусор', 'прямолинейное равноускоренное движение','реформаторский пафос','трещиноватость','правило альянса','характер спектра','подшипники','система координат Родинга-Гамильтона','возврат к стереотипам','пионерская работа','апофеоз Трога'];

shuffle($keywords);

$keywords = implode(", ", array_splice($keywords, 0,4));

$cid = rand(1,11); // category ID;
$uid = rand(0,20); // user ID

$content = "";
$n = rand(5,9);

for ($i = 1; $i<=$n; $i++) {
    shuffle($strings);
    
    $description = mb_strcut(preg_replace("/\n/ui"," ",implode(" ", array_slice($strings,0, 2))),0,220)."...";
    
    $p = "<p>".preg_replace("/\n/ui"," ",implode(" ", array_slice($strings,0, rand(4,10))))."</p>\n";
    if(rand(1,100) % 7 == 0) $p = $img.$p;
    $content .= $p;
    }

return "INSERT IGNORE INTO articles (title, keywords, description, CID,UID, content, furl) VALUES ('$title','$keywords','$description',$cid,$uid,'$content','$furl');\n";
} 
//echo "$cid $uid $furl <p>$keywords</p> $description <h1>$title</h1>$content";

////////////////////////////////////////
function connectToDB() {
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "SET NAMES utf8;";
    $rs = mysqli_query($link, $sql) or die(mysqli_error($link));

    return $link;
}
////////////////////////////////
function translit($string) {  

//	$string = preg_replace("/[^\w\d ]/ui","-",$string); // это не работает на хостинге
	$string = preg_replace("/[^а-яё0-9a-z]/ui"," ",$string); // а это работает 
	$string = preg_replace("/\s+/u","-",trim($string));
// используются правила транслита, близкие к Яндексу (доп. http://translit-online.ru/yandex.html)
	$converter =  [  
		'а' => 'a',   'б' => 'b',   'в' => 'v',  
		'г' => 'g',   'д' => 'd',   'е' => 'e',  
		'ё' => 'e',   'ж' => 'zh',  'з' => 'z',  
		'и' => 'i',   'й' => 'y',   'к' => 'k',  
		'л' => 'l',   'м' => 'm',   'н' => 'n',  
		'о' => 'o',   'п' => 'p',   'р' => 'r',  
		'с' => 's',   'т' => 't',   'у' => 'u',  
		'ф' => 'f',   'х' => 'h',   'ц' => 'c',  
		'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shch',  
		'ь' => "",  'ы' => 'y',   'ъ' => "",  
		'э' => 'eh',   'ю' => 'yu',  'я' => 'ya',  
		
		'А' => 'A',   'Б' => 'B',   'В' => 'V',  
		'Г' => 'G',   'Д' => 'D',   'Е' => 'E',  
		'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',  
		'И' => 'I',   'Й' => 'Y',   'К' => 'K',  
		'Л' => 'L',   'М' => 'M',   'Н' => 'N',  
		'О' => 'O',   'П' => 'P',   'Р' => 'R',  
		'С' => 'S',   'Т' => 'T',   'У' => 'U',  
		'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',  
		'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Shch',  
		'Ь' => "",  'Ы' => 'Y',   'Ъ' => "",  
		'Э' => 'Eh',   'Ю' => 'Yu',  'Я' => 'Ya', 
		' ' => '-', 
	];
	    
//	return iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtolower(strtr($string, $converter)));
  return strtolower(strtr($string, $converter));
} 
?>