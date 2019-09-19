<?php

function pegarJson()
{
    $file = file_get_contents("padrao.json");
    $json = json_decode($file, true);
    return $json;
 }
function montarAutoload()
{
    $json = pegarJson();
    $str = implode('","', $json['Pastas']);;
    $autoload = "<?php\n".
                " \tspl_autoload_register(function (\$nomeClasse) {\n".
                "\t\t\$folders = array(\"".
                    $str
                ."\");\n".
                "\t\tforeach (\$folders as \$folder)\n".
                    "\t\t\tif (file_exists(\$folder.DIRECTORY_SEPARATOR.\$nomeClasse.\".php\"))\n".
                "\t\t\t\trequire_once(\$folder.DIRECTORY_SEPARATOR.\$nomeClasse.\".php\");\n".
                "\t\t});\n".
            "?>";
    echo $autoload;
    $autoload_archive = fopen('autoload.php','w');
    fwrite($autoload_archive, $autoload);
  }

  function montarPDO()
  {
      $json = pegarJson();
      $host = $json["PDO"]["Host"];
      $driver = $json["PDO"]["Drive"];
      $nomeBanco = $json["PDO"]["Nome do Banco"];
      $usuario = $json["PDO"]["Usuario"];
      $senha = $json["PDO"]["Senha"];
      $pdo = "<?php \n".
                    "\trequire_once \"autoload.php\";\n".  
                    "\tclass Conexao {\n".
                        "\t\tprivate \$pdo;\n".        
                        "\t\tpublic function __construct() {\n".
                            "\t\t\t\$this->pdo = new PDO(\"".$driver.":host=".$host.";dbname=".$nomeBanco."\", \"".$usuario."\",\"".$senha."\");\n".
                        "\t\t}\n".
                        "\t\tpublic function getPdo(){\n".
                            "\t\t\t\$this->pdo = new PDO(\"".$driver.":host=".$host.";dbname=".$nomeBanco."\", \"".$usuario."\",\"".$senha."\");\n".
                            "\t\t\t\$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n".
                            "\t\t\treturn \$this->pdo;\n".
                        "\t\t}\n".
                        "\t\tpublic function setPdo(\$pdo){\n".
                            "\t\t\t\$this->pdo = \$pdo;\n".
                            "\t\t\treturn \$this;\n".
                        "\t\t}\n".
                    "\t}\n".   
                    "?>";
      $pdo_archive = fopen('conexao\pdo.php','w');
      fwrite($pdo_archive, $pdo);
    }

 function pastasPadrao()
 {
     $pastas = array("controller", "model", "view", "model\\bo", "model\\dao", "model\\dto", "conexao");
     foreach ($pastas as $value) {
        if (!file_exists($value)) {
            mkdir($value);
        }  
     }
  }
 function pastasPersonalizadas()
 {
     $pastas = pegarJson()["Pastas"];
     foreach ($pastas as $value) {
        if (!file_exists($value)) {
            mkdir($value);
        }
     }
  }
  function run()
  {
      pastasPadrao();
      pastasPersonalizadas();
      montarAutoload();
      montarPDO();
  }

run();

?>