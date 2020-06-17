<?php
    use PHPUnit\Framework\TestCase;
    class PessoaTeste extends TestCase
    {
        public function testCreatePessoa()
        {
            //cria uma pessoa. 1 = 1 para o id.
            $ref_profissao = 1;
            $value = 1;
            
            TTransaction::open('agenda');
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = 1;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $id = $pessoa->id;
                    
            $this->assertTrue(Pessoa::getPessoa($id));
            TTransaction::rollback();
        }
    }
?>
