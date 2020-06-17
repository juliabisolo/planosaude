<?php
    use PHPUnit\Framework\TestCase;
    class Tests extends TestCase
    {
        public function testCreateDeleteConsultas()
        {
            //deleta todas as consultas e insere uma. 1 = 1.
            $ref_pessoa_paciente = 1;
            $value = 1;
            
            TTransaction::open('agenda');
            $consultas = Consulta::where('ref_pessoa_paciente', '=', '1')->load();
            
            foreach ($consultas as $consulta)
            {
                $consulta->delete();
            }
            
            $consulta = new Consulta();
            $consulta->parecer = 'Teste';
            $consulta->data_hora_inicio = date('d-m-Y H:i')
            $consulta->data_hora_fim = date('d-m-Y H:i');
            $consulta->ref_pessoa_paciente = $ref_pessoa_paciente;
            $consulta->store();
                    
            $this->assertEquals( Consulta::getTotalConsultasPessoa($ref_pessoa_paciente), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoDataHoraInicioConsulta()
        {
            
        }

        public function testPreenchimentoDataHoraFimConsulta()
        {

        }

        public function testPreenchimentoPacienteConsulta()
        {

        }

        public function testConexaoBase()
        {

        }

        public function testCreateDeleteAgendamento()
        {
            //cria um agendamento e deleta todos. 0 = 0.
            TTransaction::open('agenda');
            
            $value = 0;

            $agendamento = new Agendamento();
            $agendamento->fl_ativo = true;
            $agendamento->data_hora_inicio = '2020-04-15 14:00:00';
            $agendamento->data_hora_fim = '2020-04-15 15:00:00';
            $agendamento->ref_pessoa_paciente = 1;
            $agendamento->store();

            $agendamentos = Agendamento::where('ref_pessoa_paciente', '=', '1')->load();
            foreach ($agendamentos as $agendamento)
            {
                $agendamento->delete();
            }

            $ref_pessoa_paciente = 1;
                    
            $this->assertEquals( Agendamento::getTotalAgendamentosPessoa($ref_pessoa_paciente), $value );
            TTransaction::rollback();
        }

        public function testQuantidadeAgendamentosPorPessoa()
        {
            //busca a quantidade de agendamentos por pessoa. Primeiro deleta todos, depois cria 3. 3 = 3.
            TTransaction::open('agenda');
            
            $value = 3;

            $agendamentos = Agendamento::where('ref_pessoa_paciente', '=', '1')->load();
            foreach ($agendamentos as $agendamento)
            {
                $agendamento->delete();
            }

            $agendamento = new Agendamento();
            $agendamento->fl_ativo = true;
            $agendamento->data_hora_inicio = '2020-04-15 14:00:00';
            $agendamento->data_hora_fim = '2020-04-15 15:00:00';
            $agendamento->ref_pessoa_paciente = 1;
            $agendamento->store();

            $agendamento = new Agendamento();
            $agendamento->fl_ativo = true;
            $agendamento->data_hora_inicio = '2020-04-22 14:00:00';
            $agendamento->data_hora_fim = '2020-04-22 15:00:00';
            $agendamento->ref_pessoa_paciente = 1;
            $agendamento->store();

            $agendamento = new Agendamento();
            $agendamento->fl_ativo = true;
            $agendamento->data_hora_inicio = '2020-04-29 14:00:00';
            $agendamento->data_hora_fim = '2020-04-29 15:00:00';
            $agendamento->ref_pessoa_paciente = 1;
            $agendamento->store();

            $ref_pessoa_paciente = 1;
                    
            $this->assertEquals(Agendamento::getTotalAgendamentosPessoa($ref_pessoa_paciente), $value);
            TTransaction::rollback();
        }

        public function testConflitoDataHoraAgendamento()
        {

        }

        public function testPreenchimentoDataHoraInicioAgendamento()
        {

        }

        public function testPreenchimentoDataHoraFimAgendamento()
        {

        }

        public function testPreenchimentoPacienteAgendamento()
        {

        }

        public function testCreateDeleteProfissao()
        {
            //insere uma profissão e deleta todas as profisões. 0 = 0.
            TTransaction::open('agenda');
            
            $value = 0;

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $profissoes = Profissao::where('1', '=', '1')->load();
            foreach ($profissoes as $profissao)
            {
                $profissao->delete();
            }
                    
            $this->assertEquals( Profissao::getTotal(), $value );
            TTransaction::rollback();
        }

        public function testSomenteLetrasDescricaoProfissao()
        {

        }

        public function testTamanhoPreenchimentoDescricaoProfissao()
        {

        }

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

        public function testPreenchimentoNomePessoa()
        {

        }

        public function testPreenchimentoCpfPessoa()
        {
            
        }

        public function testPreenchimentoDataNascimentoPessoa()
        {
            
        }

        public function testPreenchimentoTelefonePessoa()
        {
            
        }

        public function testPreenchimentoProfissaoPessoa()
        {
            
        }

        public function testPreenchimentoEnderecoPessoa()
        {
            
        }

        public function testPreenchimentoHistoricoPessoa()
        {
            
        }

        public function testSomenteLetrasNomePessoa()
        {
            
        }

        public function testFormatoDataNascimentoPessoa()
        {
            
        }

        public function testSomenteLetrasNomePessoa()
        {
            
        }

        public function testTamanhoPreenchimentoNomePessoa()
        {
            
        }
    }
?>