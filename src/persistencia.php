<?php


class persistencia{



  //Obter os IDs de todos os programadores coletados
  public function obterListaProgramadores()
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select id_programador from programadores where date(dta_hor_inc) = '2018-09-02'";
    $resultado = $conn->query($sql) or die (mysqli_error());


    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = $linha['id_programador'];

    }     

    return $saida;
  }


  //Obter os IDs de todos os programadores coletados
  public function obterListaProgramadoresColeta($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select id_programador , login, qtda_rep_publicos from programadores where indicador_coleta_repositorios = 'N' limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("id" => $linha['id_programador'], "login" => $linha['login'], "qtda" => $linha['qtda_rep_publicos'] );

    }     

    return $saida;
  }  


  //Obter interações do programador por repositorio
  public function obterInteracoesProgramadores($id_programador)
  {

    $saida = array();

    $saida['qtda_comentarios'] = 0;
    $saida['qtda_problemas']   = 0;
    $saida['qtda_commits']     = 0;

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select qtda_comentarios, qtda_problemas, qtda_commits from atuacao where id_programador = ".$id_programador;
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {
        $saida['qtda_comentarios']  = $saida['qtda_comentarios'] + $linha['qtda_comentarios'];
        $saida['qtda_problemas']    = $saida['qtda_problemas'] + $linha['qtda_problemas'];
        $saida['qtda_commits']      = $saida['qtda_commits'] + $linha['qtda_commits'];
    }     

    return $saida;
  }  


  //Gravar interações do programador em seus repositorios
  public function gravarInteracoesProgramadores($interacoes, $id_programador)
  {

    $saida = array();

    $saida['qtda_comentarios'] = 0;
    $saida['qtda_problemas']   = 0;
    $saida['qtda_commits']     = 0;

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "update programadores set qtda_comentarios_realizados = ".$interacoes['qtda_comentarios'].", qtda_problemas_realizados = ".$interacoes['qtda_problemas'].", qtda_commits_realizados = ".$interacoes['qtda_commits']." where id_programador = ".$id_programador;

    //Gravar na base
    $conn->query($sql);
  }    


  //Gravar interações do programador em seus repositorios
  public function gravarEstatisticasProgramadores($listaProgramadores)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");

    //para cada programador
    foreach ($listaProgramadores as $key => $value) {
  
      $sql = "update programadores set qtda_p_requests_incluidos = ".$listaProgramadores[$key]['pr'].", qtda_issues_incluidos = ".$listaProgramadores[$key]['issues'].", qtda_commits_incluidos = ".$listaProgramadores[$key]['commits']." where id_programador = ".$listaProgramadores[$key]['id']." ; ";

      //Gravar na base
      $conn->query($sql);

    }
  }    



  //Gravar interações do programador em seus repositorios
  public function gravaAtuacaoProgramadorRepositorio($id_repositorio, $nome_repositorio, $id_programador)
  {

            $conn = mysqli_connect("localhost","root","","cinpro");

              // Monta o sql com o insert em REPOSITORIOS      
              $sql = " INSERT INTO REPOSITORIOS
                      (id_repositorio, nome, dta_hor_inc)
                      VALUES
                      (".$id_repositorio.",'".$nome_repositorio."', current_timestamp);";

              //Gravar na base
              $conn->query($sql);

              // Monta o sql com o insert de atuacao
              $sql = " INSERT INTO ATUACAO
                      (id_repositorio, id_programador, dta_hor_inc)
                      VALUES
                      (".$id_repositorio.",".$id_programador.", current_timestamp);";

              //Gravar na base
              $conn->query($sql);
  }    





  //Gravar interações do programador em seus repositorios
  public function criarRegistroRepositorio($info)
  {

            $conn = mysqli_connect("localhost","root","","cinpro");

              // Monta o sql com o insert em REPOSITORIOS      
              $sql = " INSERT INTO REPOSITORIOS
                      (id_repositorio, user_dono, nome, qtda_estrelas, qtda_forks, dta_hor_inc)
                      VALUES
                      (".$info["id"].",'".$info["user_dono"]."', '".$info["nome"]."', ".$info["qtda_estrelas"].", ".$info["qtda_forks"].", current_timestamp);";

              //Gravar na base
              $conn->query($sql);
  }    





  //Gravar os dados extraídos do github
  public function gravarDados($dados)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "";


    //Para cada usuário
    foreach ($dados as $key => $value) {
      
          // Monta o sql com o insert em PROGRAMADORES
          $sql = "INSERT INTO PROGRAMADORES
                  (id_programador, login, qtda_seguidores, qtda_seguindo, qtda_rep_publicos, qtda_gists_publicos, dias_criacao, dta_hor_inc)
                  VALUES
                  (".$dados[$key]['id'].", '".$dados[$key]['login']."', ".$dados[$key]['qtda_seguidores'].",
                   ".$dados[$key]['qtda_seguindo'].", ".$dados[$key]['qtda_rep_publicos'].", ".$dados[$key]['qtda_gists_publicos'].", ".$dados[$key]['dias_criacao'].", current_timestamp);";

          //Gravar na base
          $conn->query($sql);

/*
          // Para cada repositório do programador
          foreach ($dados[$key]['repositorios'] as $key2 => $value) {

              // Monta o sql com o insert em REPOSITORIOS      
              $sql = " INSERT INTO REPOSITORIOS
                      (id_repositorio, nome, user_dono, qtda_estrelas, qtda_inscritos, qtda_downloads, qtda_visualizacoes, contem_wikis, qtda_forks, dias_criacao, qtda_comentarios, qtda_problemas, qtda_commits, qtda_p_requests, qtda_programadores, dta_hor_inc)
                      VALUES
                      (".$dados[$key]['repositorios'][$key2]['id'].",'".$dados[$key]['repositorios'][$key2]['nome']."','".$dados[$key]['repositorios'][$key2]['user_dono']."',".$dados[$key]['repositorios'][$key2]['qtda_estrelas'].",".$dados[$key]['repositorios'][$key2]['qtda_inscritos'].",".$dados[$key]['repositorios'][$key2]['qtda_downloads'].",".$dados[$key]['repositorios'][$key2]['qtda_visualizacoes'].",'".$dados[$key]['repositorios'][$key2]['contem_wikis']."',".$dados[$key]['repositorios'][$key2]['qtda_forks'].",".$dados[$key]['repositorios'][$key2]['dias_criacao'].",".$dados[$key]['repositorios'][$key2]['qtda_comentarios'].",".$dados[$key]['repositorios'][$key2]['qtda_problemas'].",".$dados[$key]['repositorios'][$key2]['qtda_commits'].",".$dados[$key]['repositorios'][$key2]['qtda_p_requests'].",".$dados[$key]['repositorios'][$key2]['qtda_programadores'].", current_timestamp);";

              //Gravar na base
              $conn->query($sql);

              // Monta o sql com o insert de atuacao
              $sql = " INSERT INTO ATUACAO
                      (id_repositorio, id_programador, qtda_comentarios, qtda_problemas, qtda_commits, dta_hor_inc)
                      VALUES
                      (".$dados[$key]['repositorios'][$key2]['id'].", ".$dados[$key]['id'].", ".$dados[$key]['repositorios'][$key2]['qtda_comentarios_usuario'].",
                       ".$dados[$key]['repositorios'][$key2]['qtda_problemas_usuario'].", ".$dados[$key]['repositorios'][$key2]['qtda_commits_usuario'].", current_timestamp);";    

              //Gravar na base
              $conn->query($sql);

          }  
*/

    }   

  }



  //Gravar os dados extraídos do github
  public function gravarDadosRepositorios($dados)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "";


    //Para cada usuário
    foreach ($dados as $key => $value) {
      

          // Para cada repositório do programador
          foreach ($dados[$key]['repositorios'] as $key2 => $value) {

              // Monta o sql com o insert em REPOSITORIOS      
              $sql = " INSERT INTO REPOSITORIOS
                      (id_repositorio, nome, user_dono, qtda_estrelas, qtda_inscritos, qtda_downloads, qtda_visualizacoes, contem_wikis, qtda_forks, dias_criacao, qtda_comentarios, qtda_problemas, qtda_commits, qtda_p_requests, qtda_programadores, dta_hor_inc)
                      VALUES
                      (".$dados[$key]['repositorios'][$key2]['id'].",'".$dados[$key]['repositorios'][$key2]['nome']."','".$dados[$key]['repositorios'][$key2]['user_dono']."',".$dados[$key]['repositorios'][$key2]['qtda_estrelas'].",".$dados[$key]['repositorios'][$key2]['qtda_inscritos'].",".$dados[$key]['repositorios'][$key2]['qtda_downloads'].",".$dados[$key]['repositorios'][$key2]['qtda_visualizacoes'].",'".$dados[$key]['repositorios'][$key2]['contem_wikis']."',".$dados[$key]['repositorios'][$key2]['qtda_forks'].",".$dados[$key]['repositorios'][$key2]['dias_criacao'].",".$dados[$key]['repositorios'][$key2]['qtda_comentarios'].",".$dados[$key]['repositorios'][$key2]['qtda_problemas'].",".$dados[$key]['repositorios'][$key2]['qtda_commits'].",".$dados[$key]['repositorios'][$key2]['qtda_p_requests'].",".$dados[$key]['repositorios'][$key2]['qtda_programadores'].", current_timestamp);";

              //Gravar na base
              $conn->query($sql);

              // Monta o sql com o insert de atuacao
              $sql = " INSERT INTO ATUACAO
                      (id_repositorio, id_programador, qtda_comentarios, qtda_problemas, qtda_commits, dta_hor_inc)
                      VALUES
                      (".$dados[$key]['repositorios'][$key2]['id'].", ".$dados[$key]['id'].", ".$dados[$key]['repositorios'][$key2]['qtda_comentarios_usuario'].",
                       ".$dados[$key]['repositorios'][$key2]['qtda_problemas_usuario'].", ".$dados[$key]['repositorios'][$key2]['qtda_commits_usuario'].", current_timestamp);";    

              //Gravar na base
              $conn->query($sql);

              //Atualiza o indicador
              $sql = "UPDATE PROGRAMADORES set coleta_repositorio_realizada = 'S' where id_programador = ".$dados[$key]['id'];
              $conn->query($sql);

          }  


    }   

  }



  //Gravar os dados extraídos do github
  public function gravarRepositoriosDosProgramadores($dados)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "";


    //Para cada programador
    foreach ($dados as $key => $value) {

        if(isset($dados[$key]['repositorios']))
        {
          // Para cada repositório do programador
          foreach ($dados[$key]['repositorios'] as $key2 => $value) {

              // Monta o sql com o insert de atuacao
              $sql = " INSERT INTO ATUACAO
                      (id_repositorio, id_programador, usuario_dono, node_id, dta_hor_inc)
                      VALUES
                      (".$dados[$key]['repositorios'][$key2]['id'].", ".$dados[$key]['id'].", '".$dados[$key]['repositorios'][$key2]['usuario_dono']."','
                        ".$dados[$key]['repositorios'][$key2]['node_id']."', current_timestamp);";    

              //Gravar na base
              $conn->query($sql);

              $sql = "update programadores set indicador_coleta_repositorios = 'S' where id_programador = ".$dados[$key]['id'];

              $conn->query($sql);
          }  
        }


    }   

  }



  //Obter os IDs de todos os programadores coletados
  public function obterProgramadoresRepositorios($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select a.id_repositorio, a.id_programador , a.node_id, b.login , c.nome from atuacao a, programadores b , repositorios c  where a.id_programador = b.id_programador and a.id_repositorio = c.id_repositorio  and a.qtda_commits is null limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("id_programador" => $linha['id_programador'], "id_repositorio" => $linha['id_repositorio'], "login" => $linha['login'], "node_id" => trim($linha['node_id']) );

    }     

    return $saida;
  }  



  //Obter os IDs de todos os programadores coletados
  public function obterProgramadoresParaBuscaRepositorios($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select a.id_repositorio, a.usuario_dono from atuacao a where not exists ( select * from repositorios b where b.id_repositorio = a.id_repositorio )  limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("id_repositorio" => $linha['id_repositorio'], "usuario_dono" => $linha['usuario_dono']);

    }     

    return $saida;
  }  



  //Obter os IDs de todos os programadores coletados
  public function obterProgramadoresRestantes($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select login , id_programador from programadores where indicador_coleta_repositorios = 'N'  limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("login" => $linha['login'], "id" => $linha['id_programador'] );

    }     

    return $saida;
  }    



  //Obter os IDs de todos os obterRepositoriosRestantes
  public function obterRepositoriosRestantes($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select id_repositorio, user_dono, nome from repositorios where qtda_inscritos is null  limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("user_dono" => $linha['user_dono'], "id_repositorio" => $linha['id_repositorio'], "nome" => $linha['nome'] );

    }     

    return $saida;
  }    






  //Gravar interações do programador em seus repositorios
  public function atualizarAtuacaoProgramador($programadorRepositorio)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "update atuacao set qtda_p_requests = ".$programadorRepositorio['qtda_pr']." , qtda_commits = ".$programadorRepositorio['qtda_commits']." , qtda_issues = ".$programadorRepositorio['qtda_issues']." where id_programador = ".$programadorRepositorio['id_programador']." and id_repositorio = ".$programadorRepositorio['id_repositorio'];

    //Gravar na base
    $conn->query($sql);
  }  


  //Gravar interações do programador em seus repositorios
  public function atualizaQtdaInscritos($qtda, $id)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "update repositorios set qtda_inscritos = ".$qtda." where id_repositorio = ".$id;

    //Gravar na base
    $conn->query($sql);
  }  





  //Obter os IDs de todos os repositorios
  public function obterRepositorios($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select a.id_repositorio, a.usuario_dono from atuacao a where not exists ( select * from repositorios b where b.id_repositorio = a.id_repositorio )  group by a.id_repositorio, a.usuario_dono limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("id_repositorio" => $linha['id_repositorio'], "usuario_dono" => $linha['usuario_dono']);

    }     

    return $saida;
  }  




    //Gravar os dados extraídos do github
  public function gravarNomeRepositorio($id, $nome)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");

    // Monta o sql com o insert de atuacao
    $sql = " INSERT INTO repositorios
            (id_repositorio, nome, dta_hor_inc)
            VALUES
            (".$id.",'".$nome."' , current_timestamp);";    

    //Gravar na base
    $conn->query($sql);

  }



  //Obter os IDs de todos os programadores coletados
  public function obterProgramadoresNormalizacao($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "
    select id_programador, qtda_seguidores, qtda_seguindo, qtda_rep_publicos, qtda_p_requests_incluidos, qtda_issues_incluidos, qtda_commits_incluidos
    from programadores
    where qtda_seguidores_n is null limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array( "id_programador"            => $linha['id_programador'],
                        "qtda_seguidores"           => $linha['qtda_seguidores'], 
                        "qtda_seguindo"             => $linha['qtda_seguindo'], 
                        "qtda_rep_publicos"         => $linha['qtda_rep_publicos'], 
                        "qtda_p_requests_incluidos" => $linha['qtda_p_requests_incluidos'], 
                        "qtda_issues_incluidos"     => $linha['qtda_issues_incluidos'], 
                        "qtda_commits_incluidos"    => $linha['qtda_commits_incluidos'] );
    }     

    return $saida;
  }  


  //Obter os IDs de todos os programadores coletados
  public function obterValoloresLimiteProgramadores()
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "
    select
      max(qtda_seguidores) as max_qtda_seguidores, min(qtda_seguidores) as min_qtda_seguidores,
      max(qtda_seguindo) as max_qtda_seguindo, min(qtda_seguindo) as min_qtda_seguindo,
      max(qtda_rep_publicos) as max_qtda_rep_publicos, min(qtda_rep_publicos) as min_qtda_rep_publicos,
      max(qtda_p_requests_incluidos) as max_qtda_p_requests_incluidos, min(qtda_p_requests_incluidos) as min_qtda_p_requests_incluidos,
      max(qtda_issues_incluidos) as max_qtda_issues_incluidos, min(qtda_issues_incluidos) as min_qtda_issues_incluidos,
      max(qtda_commits_incluidos) as max_qtda_commits_incluidos, min(qtda_commits_incluidos) as min_qtda_commits_incluidos
    from
      programadores";
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida = array( "max_qtda_seguidores"             => $linha['max_qtda_seguidores'],
                      "min_qtda_seguidores"             => $linha['min_qtda_seguidores'], 
                      "max_qtda_seguindo"               => $linha['max_qtda_seguindo'], 
                      "min_qtda_seguindo"               => $linha['min_qtda_seguindo'], 
                      "max_qtda_rep_publicos"           => $linha['max_qtda_rep_publicos'], 
                      "min_qtda_rep_publicos"           => $linha['min_qtda_rep_publicos'], 
                      "max_qtda_p_requests_incluidos"   => $linha['max_qtda_p_requests_incluidos'],
                      "min_qtda_p_requests_incluidos"   => $linha['min_qtda_p_requests_incluidos'], 
                      "max_qtda_issues_incluidos"       => $linha['max_qtda_issues_incluidos'], 
                      "min_qtda_issues_incluidos"       => $linha['min_qtda_issues_incluidos'], 
                      "max_qtda_commits_incluidos"      => $linha['max_qtda_commits_incluidos'], 
                      "min_qtda_commits_incluidos"      => $linha['min_qtda_commits_incluidos']);
                      
    }     

    return $saida;
  }    



  //Obter os IDs de todos os programadores coletados
  public function atualizarNormalizacaoProgramador($programador)
  {

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "
    update
      programadores
    set
      qtda_seguidores_n           = ".$programador['qtda_seguidores_n'].", 
      qtda_seguindo_n             = ".$programador['qtda_seguindo_n'].", 
      qtda_rep_publicos_n         = ".$programador['qtda_rep_publicos_n'].", 
      qtda_p_requests_incluidos_n = ".$programador['qtda_p_requests_incluidos_n'].", 
      qtda_issues_incluidos_n     = ".$programador['qtda_issues_incluidos_n'].", 
      qtda_commits_incluidos_n    = ".$programador['qtda_commits_incluidos_n']."
    where 
      id_programador = ".$programador['id_programador'];
    
    //Gravar na base
    $conn->query($sql) or die (mysqli_error());

    return true;
  }    

  //Obter os IDs de todos os programadores coletados
  public function atualizaIndicadorColetaRepositorios($programador)
  {

              $conn = mysqli_connect("localhost","root","","cinpro");

              $sql = "update programadores set indicador_coleta_repositorios = 'S' where id_programador = ".$programador;

              $conn->query($sql);
  }


  public function atualizaDiasCriacaoEstatistica($id, $dias, $estatistica)
  {

              $conn = mysqli_connect("localhost","root","","cinpro");

              $sql = "update repositorios set dias_criacao = ".$dias.", estatistica_sucesso = ".$estatistica." where id_repositorio = ".$id;

              $conn->query($sql);
  }



  public function atualizaEstatistica($id, $estatistica)
  {

              $conn = mysqli_connect("localhost","root","","cinpro");

              $sql = "update repositorios set estatistica_sucesso = ".$estatistica." where id_repositorio = ".$id;

              $conn->query($sql);
  }



  //Obter os IDs de todos os programadores coletados
  public function obterRepositoriosColetaDias($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select id_repositorio, nome, user_dono, qtda_estrelas from repositorios where estatistica_sucesso is null limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("id_repositorio" => $linha['id_repositorio'], "nome" => $linha['nome'], "user_dono" => $linha['user_dono'], "qtda_estrelas" => $linha['qtda_estrelas']);

    }     

    return $saida;
  }    


  //Obter os IDs de todos os programadores coletados
  public function obterRepositoriosColetaSoma($limite)
  {

    $saida = array();

    //Conexão ao MYSQL 
    $conn = mysqli_connect("localhost","root","","cinpro");
    $sql  = "select id_repositorio, qtda_estrelas, qtda_forks, dias_criacao from repositorios where estatistica_sucesso is null limit ".$limite;
    
    $resultado = $conn->query($sql) or die (mysqli_error());

    while ($linha=mysqli_fetch_array($resultado))
    {

      $saida[] = array("id_repositorio" => $linha['id_repositorio'], "qtda_estrelas" => $linha['qtda_estrelas'], "qtda_forks" => $linha['qtda_forks'], "dias_criacao" => $linha['dias_criacao']);

    }     

    return $saida;
  }      



}
  