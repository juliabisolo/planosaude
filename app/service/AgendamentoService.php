<?php

    class AgendamentoService
    {
    	public static function validaDataHoraInicioAgendamento($ref_agendamento)
    	{
    		$agendamento = new Agendamento($ref_agendamento);

    		if(isset($agendamento->data_hora_inicio) AND $agendamento->data_hora_inicio != NULL)
    		{
    			return true;
    		}

    		return false;
    	}

    	public static function validaDataHoraFimAgendamento($ref_agendamento)
    	{
    		$agendamento = new Agendamento($ref_agendamento);

    		if(isset($agendamento->data_hora_fim) AND $agendamento->data_hora_fim != NULL)
    		{
    			return true;
    		}

    		return false;
    	}

        public static function validaPaciente($ref_agendamento)
        {
            $agendamento = new Agendamento($ref_agendamento);

            if(isset($agendamento->ref_pessoa_paciente) AND $agendamento->ref_pessoa_paciente != NULL)
            {
                return true;
            }

            return false;
        }
    }