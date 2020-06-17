CREATE TABLE profissao (id serial primary key not null, descricao text not null);

CREATE TABLE pessoa (id serial primary key not null, nome text not null, cpf text not null, dt_nascimento date not null, endereco text not null, telefone text not null, cor_agenda text not null, historico text not null, ref_profissao integer, fl_ativo boolean default true);

CREATE TABLE agendamento (id serial primary key not null, fl_ativo boolean default true, data_hora_inicio timestamp not null, data_hora_fim timestamp not null, ref_pessoa_paciente integer references pessoa(id));

CREATE TABLE consulta (id serial primary key not null, parecer text not null, data_hora_inicio timestamp not null, data_hora_fim timestamp not null, ref_agendamento integer references agendamento(id), ref_pessoa_paciente integer references pessoa(id));


