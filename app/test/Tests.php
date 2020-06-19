<?php
    use PHPUnit\Framework\TestCase;
    class Tests extends TestCase
    {
        public function testCreateConsultas()
        {
            //deleta todas as consultas e insere uma. 1 = 1.
            $value = 1;
            
            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();
            
            $consulta = new Consulta();
            $consulta->parecer = 'Teste';
            $consulta->data_hora_inicio = date('d-m-Y H:i');
            $consulta->data_hora_fim = date('d-m-Y H:i');
            $consulta->ref_pessoa_paciente = $pessoa->id;
            $consulta->store();
                    
            $this->assertEquals( Consulta::getTotalConsultasPessoa($pessoa->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoDataHoraInicioConsulta()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $consulta = new Consulta();
            $consulta->parecer = 'Teste';
            $consulta->data_hora_inicio = date('d-m-Y H:i');
            $consulta->data_hora_fim = date('d-m-Y H:i');
            $consulta->ref_pessoa_paciente = $pessoa->id;
            $consulta->store();

            $this->assertEquals( ConsultaService::validaDataHoraInicioConsulta($consulta->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoDataHoraFimConsulta()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $consulta = new Consulta();
            $consulta->parecer = 'Teste';
            $consulta->data_hora_inicio = date('d-m-Y H:i');
            $consulta->data_hora_fim = date('d-m-Y H:i');
            $consulta->ref_pessoa_paciente = $pessoa->id;
            $consulta->store();

            $this->assertEquals( ConsultaService::validaDataHoraInicioConsulta($consulta->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoPacienteConsulta()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $consulta = new Consulta();
            $consulta->parecer = 'Teste';
            $consulta->data_hora_inicio = date('d-m-Y H:i');
            $consulta->data_hora_fim = date('d-m-Y H:i');
            $consulta->ref_pessoa_paciente = $pessoa->id;
            $consulta->store();

            $this->assertEquals( ConsultaService::validaDataHoraFimConsulta($consulta->id), $value );
            TTransaction::rollback();
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

        public function testPreenchimentoDataHoraInicioAgendamento()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $agendamento = new Agendamento();
            $agendamento->fl_ativo = true;
            $agendamento->data_hora_inicio = '2020-04-15 14:00:00';
            $agendamento->data_hora_fim = '2020-04-15 15:00:00';
            $agendamento->ref_pessoa_paciente = $pessoa->id;
            $agendamento->store();

            $this->assertEquals( AgendamentoService::validaDataHoraInicioAgendamento($agendamento->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoDataHoraFimAgendamento()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $agendamento = new Agendamento();
            $agendamento->fl_ativo = true;
            $agendamento->data_hora_inicio = '2020-04-15 14:00:00';
            $agendamento->data_hora_fim = '2020-04-15 15:00:00';
            $agendamento->ref_pessoa_paciente = $pessoa->id;
            $agendamento->store();

            $this->assertEquals( AgendamentoService::validaDataHoraFimAgendamento($agendamento->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoPacienteAgendamento()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $agendamento = new Agendamento();
            $agendamento->fl_ativo = true;
            $agendamento->data_hora_inicio = '2020-04-15 14:00:00';
            $agendamento->data_hora_fim = '2020-04-15 15:00:00';
            $agendamento->ref_pessoa_paciente = $pessoa->id;
            $agendamento->store();

            $this->assertEquals( AgendamentoService::validaPaciente($agendamento->id), $value );
            TTransaction::rollback();
        }

        public function testCreateDeleteProfissao()
        {
            //insere uma profissão e deleta todas as profisões. 0 = 0.
            TTransaction::open('agenda');
            
            $value = 2;

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $pessoa2 = new Pessoa();
            $pessoa2->nome = 'João';
            $pessoa2->cpf = date('048.353.090-57');
            $pessoa2->dt_nascimento = ('1998-10-10');
            $pessoa2->endereco = 'Rua Teste Auto';
            $pessoa2->telefone = '(51)00000-0000';
            $pessoa2->cor_agenda = '#1a5d3e';
            $pessoa2->historico = 'Teste histórico Ana Maria';
            $pessoa2->ref_profissao = $profissao->id;
            $pessoa2->fl_ativo = true;
            $pessoa2->store();

            $count = Pessoa::where('ref_profissao', '=', $profissao->id)->count();

            $this->assertEquals( $count, $value );
            TTransaction::rollback();
        }

        public function testTamanhoPreenchimentoDescricaoProfissao()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $this->assertEquals( Profissao::validaTamanho($profissao->id), $value );
            
            TTransaction::rollback();
        }

        public function testCreatePessoa()
        {
            $value = 1;
            
            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $count = Pessoa::where('id', '=', $pessoa->id)->count();
                    
            $this->assertEquals($count, $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoNomePessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( PessoaService::validaPreenchimentoNome($pessoa->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoCpfPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( PessoaService::validaPreenchimentoCpf($pessoa->id), $value );
            TTransaction::rollback();

        }

        public function testPreenchimentoDataNascimentoPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( PessoaService::validaPreenchimentoDataNascimento($pessoa->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoTelefonePessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( PessoaService::validaPreenchimentoTelefone($pessoa->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoProfissaoPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( PessoaService::validaPreenchimentoProfissao($pessoa->id), $value );
            TTransaction::rollback();
        }

        public function testPreenchimentoEnderecoPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( PessoaService::validaPreenchimentoEndereco($pessoa->id), $value );
            TTransaction::rollback();

        }

        public function testPreenchimentoHistoricoPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();
            
            $pessoa = new Pessoa();
            $pessoa->nome = 'Ana Maria';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( PessoaService::validaPreenchimentoHistorico($pessoa->id), $value );
            TTransaction::rollback();
        }

        public function testTamanhoPreenchimentoNomePessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $pessoa = new Pessoa();
            $pessoa->nome = 'Júlia Noschang Bisolo Estela Elis Krein';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( Pessoa::validaTamanhoNome($pessoa->id), $value );
            
            TTransaction::rollback();
        }

        public function testTamanhoPreenchimentoCpfPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $pessoa = new Pessoa();
            $pessoa->nome = 'Júlia Noschang Bisolo Estela Elis Krein';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( Pessoa::validaTamanhoCpf($pessoa->id), $value );
            
            TTransaction::rollback();
        }

        public function testTamanhoPreenchimentoEnderecoPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $pessoa = new Pessoa();
            $pessoa->nome = 'Júlia Noschang Bisolo Estela Elis Krein';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( Pessoa::validaTamanhoEndereco($pessoa->id), $value );
            
            TTransaction::rollback();
        }

        public function testTamanhoPreenchimentoTelefonePessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $pessoa = new Pessoa();
            $pessoa->nome = 'Júlia Noschang Bisolo Estela Elis Krein';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( Pessoa::validaTamanhoTelefone($pessoa->id), $value );
            
            TTransaction::rollback();
        }

        public function testTamanhoPreenchimentoHistoricoPessoa()
        {
            $value = true;

            TTransaction::open('agenda');

            $profissao = new Profissao();
            $profissao->descricao = 'Profissão teste auto.';
            $profissao->store();

            $pessoa = new Pessoa();
            $pessoa->nome = 'Júlia Noschang Bisolo Estela Elis Krein';
            $pessoa->cpf = date('048.353.090-57');
            $pessoa->dt_nascimento = ('1998-10-10');
            $pessoa->endereco = 'Rua Teste Auto';
            $pessoa->telefone = '(51)00000-0000';
            $pessoa->cor_agenda = '#1a5d3e';
            $pessoa->historico = 'Teste histórico Ana Maria';
            $pessoa->ref_profissao = $profissao->id;
            $pessoa->fl_ativo = true;
            $pessoa->store();

            $this->assertEquals( Pessoa::validaTamanhoHistorico($pessoa->id), $value );
            
            TTransaction::rollback();
        }
    }
?>