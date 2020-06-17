<?php
    use PHPUnit\Framework\TestCase;
    class ProfissaoTeste extends TestCase
    {
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
    }
?>
