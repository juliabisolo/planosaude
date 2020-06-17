<?php
use PHPUnit\Framework\TestCase;
class ProfissaoTeste extends TestCase
{
    public function testCreateDeleteConsultas()
    {
        $ref_pessoa_paciente = 1;
        $value      = 1;
        
        TTransaction::open('agenda');
        $consultas = Consulta::where('ref_pessoa_paciente', '=', '1')->load();
        foreach ($consultas as $consulta)
        {
            $consulta->delete();
        }
        
        $consulta = new Consulta;
        $consulta->parecer = 'Teste';
        $consulta->data_hora_inicio = date('d-m-Y H:i')
        $consulta->data_hora_fim = date('d-m-Y H:i');
        $consulta->ref_pessoa_paciente = $ref_pessoa_paciente;
        $consulta->store();
                
        $this->assertEquals( Consulta::getTotalConsultasPessoa($ref_pessoa_paciente), $value );
        TTransaction::rollback();
    }
}
?>