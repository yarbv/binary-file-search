<?php
function createFile($countElem)
{
    $str = '';
    for ($i = 1; $i <= $countElem;$i++){
        $str .= "Key".$i."\tvalue".$i."\x0A";
    }
    file_put_contents (realpath(__DIR__)."\\testFile" ,$str);
}

function searchBinary($nameFile, $valueKey, $moving = "up", $pos = NULL, $pos1=NULL)
{
    if($pos === NULL){
        echo 'Размер файла : ' . filesize('testFile') . ' байтов<br>';
        $pos = (int) round(filesize('testFile') / 2);
    }
    if($pos1 === NULL){
        $pos1 = (int)  round($pos / 2);
    }
    $pos2 = $pos;
    $f = fopen($nameFile, "rb");
    fseek($f, $pos2, SEEK_SET);
    if($moving == "up"){
        do {
            $i = 1;
            fseek($f, $pos2, SEEK_SET);
            $pos2 = $pos2 + $i;
        } while ((chr('10') != ($char = fgetc($f))));
    } else {
        while ((chr('10') != ($char = fgetc($f))) ) {
            $i = 1;
            if((ftell($f) == 1)) {
                $i = 0;
            }
            fseek($f, $pos2 - $i, SEEK_SET);
            $pos2 = $pos2 - $i;
            if($i == 0) {
                break;
            }
        }
    }
    $arrTest = fscanf($f, "%s\t%s\x0A");
    fclose($f);
    $pos3 = $pos;
    switch (strnatcmp($valueKey, $arrTest[0])) {
        case 1:
            $pos3 += (int) $pos1;
            $pos1 = (int)  round($pos1 / 2);

            if ($pos3 >= filesize('testFile')){
                exit('Данный ключ не найден');
            }

            $moving = "up";
            searchBinary($nameFile, $valueKey,$moving,$pos3,$pos1);
            break;
        case -1:
            $pos -= (int) $pos1;
            $pos1 = (int)  round($pos1 / 2);
            if ($pos < 0){
                exit('Данный ключ не найден');
            }
            $moving = "down";
            searchBinary($nameFile, $valueKey,$moving,$pos,$pos1);
            break;
        case 0:
            echo "Готово!<br>";
            echo "Ключ ".$arrTest[0]." значение ".$arrTest[1];
            break;
    }
}

createFile(9000000);
searchBinary("testFile", "Key9000000");
