<?php

    class PessoaService
    {
    	public static function validaPreenchimentoNome($ref_pessoa)
    	{
    		$pessoa = new Pessoa($ref_pessoa);

    		if(isset($pessoa->nome) AND $pessoa->nome != NULL)
    		{
    			return true;
    		}

    		return false;
    	}

    	public static function validaPreenchimentoCpf($ref_pessoa)
    	{
    		$pessoa = new Pessoa($ref_pessoa);

    		if(isset($pessoa->cpf) AND $pessoa->cpf != NULL)
    		{
    			return true;
    		}

    		return false;
    	}

        public static function validaPreenchimentoDataNascimento($ref_pessoa)
        {
            $pessoa = new Pessoa($ref_pessoa);

            if(isset($pessoa->dt_nascimento) AND $pessoa->dt_nascimento != NULL)
            {
                return true;
            }

            return false;
        }

        public static function validaPreenchimentoTelefone($ref_pessoa)
        {
            $pessoa = new Pessoa($ref_pessoa);

            if(isset($pessoa->telefone) AND $pessoa->telefone != NULL)
            {
                return true;
            }

            return false;
        }

        public static function validaPreenchimentoProfissao($ref_pessoa)
        {
            $pessoa = new Pessoa($ref_pessoa);

            if(isset($pessoa->ref_profissao) AND $pessoa->ref_profissao != NULL)
            {
                return true;
            }

            return false;
        }

        public static function validaPreenchimentoEndereco($ref_pessoa)
        {
            $pessoa = new Pessoa($ref_pessoa);

            if(isset($pessoa->endereco) AND $pessoa->endereco != NULL)
            {
                return true;
            }

            return false;
        }

        public static function validaPreenchimentoHistorico($ref_pessoa)
        {
            $pessoa = new Pessoa($ref_pessoa);

            if(isset($pessoa->historico) AND $pessoa->historico != NULL)
            {
                return true;
            }

            return false;
        }
    }