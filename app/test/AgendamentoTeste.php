<?php
    use PHPUnit\Framework\TestCase;
    class AgendamentoTeste extends TestCase
    {
        public function testCreateAgendamento()
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
    }
?>
