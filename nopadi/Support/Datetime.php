<?php
/*
*
*Author: Paulo Leonardo da Silva Cassimiro
*
*/
namespace Nopadi\Support;

class Datetime
{
    /*Calcula a quantidade de dias em um intervalo entre duas datas*/
	public function sumDay($date_i,$date_f)
	{
    
	 $date = strtotime($date_f) - strtotime($date_i);
     return floor($date / (60 * 60 * 24)); 
	
	}
	private function convert($x)
	{
		if(is_string($x)){
			
			$ops =  str_ireplace(['y','a','m'],['y|','y|','m|'],$x);
			$ops = explode('|',$ops);
			$op1 = str_ireplace(['y','m'],[365,30],$ops[0]);
			$op2 = isset($ops[1]) ? $ops[1] : 1;
	        $op = intval($op1) * intval($op2);
			return $op;
			
		}else{
			return $x;
		}
	}
	
	/*Soma dias a uma data e retorna a nova data*/
	public function sumQtd($date,$qtd)
	{
	   return $this->op($date,$qtd,'+');
	}
	
	/*Subtrai dias a uma data e retorna a nova data*/
	public function subQtd($date,$qtd)
	{
	   return $this->op($date,$qtd,'-');
	}
	
	private function op($date,$qtd,$op)
	{
		return date("Y-m-d", strtotime("{$op}{$qtd} days",strtotime($date)));
	}
	public function current($num=null,$type='d')
	{
		$date =  NP_DATE;
		if(is_null($num)){
			return $date;
		}else{
			return $this->sum($date,$num,$type);
		}
	}
	/*Soma dias,meses ou anos a uma data e retorna a nova data*/
	public function sum($date,$num,$type='d')
	{
		$num = $this->convert($num);
        switch ($type)
		{
          case 'd': $type = ' day'; break;
          case 'm': $type = ' month'; break;
          case 'y': $type = ' year'; break;
        }
	
      $date = "+".$num.$type;
      return date('Y-m-d', strtotime($date));
   }
   public function sumH($hora)
   {

      // Soma 30 Minutos
      return date('H:i:s', strtotime("{$hora} minute", strtotime($hora)));
	   
   }
}