<?php

namespace App\Classes;

class Format 
{

	//LIMPIAMOS EL STRING DE ESPACIOS, SALTOS DE LINEA,...
	public function cleanData($value)
	{
		$value = preg_replace('/\xA0/u', ' ', $value); //Elimina %C2%A0 del principio y resto de espacios
		$value = trim(str_replace(array("\r", "\n"), '', $value)); //elimina saltos de linea al principio y final
		return $value;
	}	


	//FORMATEA UNA FECHA DESDE EJ. '16 de septiembre de 2016' 
    public function date($value)
    {
    	$value = urldecode(str_replace('%E2%80%83', '', urlencode($value)));
    	preg_match_all('/(\d)|(\w)/', $value, $matches);
    	$numbers = implode($matches[1]);
    	$year = substr($numbers, -4);
    	$day = substr($numbers, 0, -4);
		$letters = implode($matches[2]);
		$month = substr(substr($letters, 0, -2), 2);
		$translateMonth = [
			'enero'		=> '01',
			'febrero'	=> '02',
			'marzo'		=> '03',
			'abril'		=> '04',
			'mayo'		=> '05',
			'junio'		=> '06',
			'julio'		=> '07',
			'agosto'	=> '08',
			'septiembre'=> '09',
			'octubre'	=> '10',
			'noviembre'	=> '11',
			'diciembre'	=> '12',
		];
		$value = $year . '-' . $translateMonth[$month] . '-'. $day;
    	return $value;
    }

	//FORMATEA UNA FECHA DESDE EJ. 'September 30–October 2, 2016' 
    public function date2($value)
    {
    	$value = str_replace(chr(150), '-–', $value); //hay un guion que no lo pilla, se llama em_dash
    	$arr1 = explode(',', $value);
    	$year = trim($arr1[1]);
    	$arr2 = explode('-', $arr1[0]);
    	$arr3 = explode(' ', $arr2[1]);
    	$month = preg_replace("/[^a-zA-Z0-9]+/", "", $arr3[0]); //queda un guion, lo eliminamos
    	$day = $arr3[1];
    	$translateMonth = [
			'January'	=> '01',
			'February'	=> '02',
			'March'		=> '03',
			'April'		=> '04',
			'May'		=> '05',
			'June'		=> '06',
			'July'		=> '07',
			'August'	=> '08',
			'September'=> '09',
			'October'	=> '10',
			'November'	=> '11',
			'December'	=> '12',
		];
		$value = $year . '-' . $translateMonth[$month] . '-'. $day;
    	return $value;
    }


    //DEVUELVE EL TEXTO SI EXISTE LA CLASE CSS O EL DEFAULT(0, '',...) SI NO
	public function getElementIfExist($element, $class, $default) 
	{
		if ($element->filter($class)->count()) {
			return $element->filter($class)->text(); 
		} else {
			return $default;
		}	
	}


	//QUITA PUNTOS Y OTROS CARÁCTERES Y DEVUELVE EL ENTERO
	public function Integer($value)
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}


	public function float($value)
	{
		return (float) str_replace(',', '.', $value);
	}


	// /es/film422703.html : ELIMINA TODO MENOS EL ID
	public function faId($value)
	{
		$value = substr($value, 8); //elimina 8 primeros carácteres
		$value = substr($value, 0, -5); //elimina 5 últimos carácteres
		$value = $this->Integer($value);
		return $value;
	}


	//QUITA (TV) DEL FINAL SI LO TIENE
	public function faTitle($value)
	{
		return trim(preg_replace('/\(TV\)$/', '', $value));
	}


	//QUITAMOS (AKA) AL FINAL Y DEMAS TEXTOS ENTRE PARENTESIS
	public function faOriginal($value)
	{
        $value = $this->cleanData($value);
        $value = preg_replace('/aka$/', '', $value); //quitamos 'aka' del final si lo tiene
        $value = preg_replace("/\([^)]+\)/","",$value); //quitamos cualquier texto entre parentesis
        $value = trim($value); //si hemos quitado aka ha quedado un espacio al final, lo quitamos
        return $value;		
	}


	//LIMPIAMOS Y QUITAMOS (FILMAFFINITY) DEL FINAL SI LO TIENE
	public function faReview($value)
    {
        $value = $this->cleanData($value);
        $value = trim(preg_replace('/\(FILMAFFINITY\)$/', '', $value));
        return $value;
    }


   	public function faCritics($value)
    {
        $value = preg_split("/\\r\\n|\\r|\\n/", $value); //trozeamos el string por todos los saltos de linea (/n o /r)
        $value = array_map('trim', $value);
        $value = array_filter($value); //Elimina elementos vacíos
        $value = array_map(function($value) { 
                $value = preg_replace('/Mostrar(.*)críticas más/','',$value); //Elimina Mostrar ?? críticas mas
                $value = preg_replace('/Puntuación(.*)\)/','',$value); //Elimina Puntuacion ? (sobre ?)
                $value = trim(preg_replace('/\xA0/u', ' ', $value)); //Elimina %C2%A0 del principio y resto de espacios
                return $value;
            }, $value);
        $value = array_filter($value); //Vuelve a eliminar elementos vacios (borra lo que queda al quitar 'Mostrar ?? crticitas mas')
        if (count($value) % 2 != 0) { //Si es un array impar es que esta mal y fallara, para evitarlo provisionalmente eliminamos el último
            array_pop($value);
        }

        $value = array_chunk($value, 2);

        foreach ($value as $critic) {

        	if (substr_count($critic[1], ':') == 1) { //troceamos el autor en nombre y alias, casi siempre lo separa el caracter :
        		$critic[1] = explode(':', $critic[1]);
        		$critic[1] = array_map('trim', $critic[1]);
        	} else { //si en alguna no hay : o hay mas de uno anulamos el corte y en su lugar lo repetimos
        		$critic[1]= [$critic[1], $critic[1]];
        	}

        	$critic[0] = str_replace('"', '', $critic[0]); //eliminamos comillas dobles que tienen todas las criticas al principio y fin
        	if (strlen($critic[0]) > 200) {
        		$critic[0] = substr($critic[0], 0, 196) . '...'; //si es demasiado larga la cortamos
        		$critic[0] = mb_convert_encoding($critic[0], 'UTF-8', 'UTF-8');
        	}

        	//Y construimos un nuevo array con todos los datos
        	$result[] = [
        		'text' 	=> $critic[0],
        		'author'=> [
        			'name'  => $critic[1][0],
        			'alias' => $critic[1][1],
        		]
        	];
        }

        return isset($result) ? $result : NULL;
    }




}
