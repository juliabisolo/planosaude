<?php

    class ConsultaService
    {
    	public static function validaDataHoraInicioConsulta($ref_consulta)
    	{
    		$consulta = new Consulta($ref_consulta);

    		if(isset($consulta->data_hora_inicio) AND $consulta->data_hora_inicio != NULL)
    		{
    			return true;
    		}

    		return false;
    	}

    	public static function validaDataHoraFimConsulta($ref_consulta)
    	{
    		$consulta = new Consulta($ref_consulta);

    		if(isset($consulta->data_hora_fim) AND $consulta->data_hora_fim != NULL)
    		{
    			return true;
    		}

    		return false;
    	}
    }