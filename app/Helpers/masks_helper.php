<?php 


function mask_cpf(string $cpf): string
{
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
}



function mask_telefone(string $telefone): string
{
    $telefone = preg_replace('/\D/', '', $telefone);

    if (strlen($telefone) == 10) {
        return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $telefone);
    } elseif (strlen($telefone) == 11) {
        return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $telefone);
    } else {
        return $telefone;
    }
}


function mask_desc($descricao) 
{
    if (strlen($descricao) > 40) {
        return substr($descricao, 0, 40) . '...';
    } else {
        return $descricao; 
    }
}

function mask_title($nome) 
{
    if (strlen($nome) > 20) {
        return substr($nome, 0, 20) . '...';
    } else {
        return $nome; 
    }
}

function mask_valor($numero) 
{
    $valor = number_format($numero, 2, ',', '.');
    return $valor;
}