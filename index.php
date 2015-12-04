<?php

header('Content-Type: text/html; charset=UTF-8');

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 't';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname) or die('Error connecting to mysql');

mysqli_set_charset($conn,"utf8");

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

$tablename = '';
$attributes = '';
$values = '';

if ((isset($_POST['tablename'])) and (isset($_POST['attributes'])) and (isset($_POST['values']))) {
    $tablename = test_input($_POST['tablename']);
    $attributes = test_input($_POST['attributes']);
    $values = test_input($_POST['values']);
}

if (($tablename != '') and ($attributes != '') and ($values != '')) {

    $query_insert = "INSERT INTO $tablename($attributes) VALUES ($values)";

    if ($conn->query($query_insert) === TRUE) {
        echo "New record created successfully! :-) ";
    } else {
        echo 'Error:' . $conn->error;
    }
}


?>


<!DOCTYPE html>
<html>
<head>
<title>Eventos Lusica IBD</title>
</head>

<body>

<h1 style='text-align:center;'>Delivery 2: Group ibd07</h1>

<br><hr><br>

<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = 'post'>
    <b>INSERT INTO (ex: Utilizador):</b> <input type = 'text' name='tablename'><br><br>
    <b>Atribute Lists (ex: id_facebook, nr_feed, nome):</b> <input type = 'text' name='attributes'><br><br>
    <b>VALUES (ex: 10, '541', 'Jorge Silva'):</b> <input type = 'text' name='values'><br><br>
    <input type='submit'value='Inserir!'>
</form>

<hr><br>

<!-- ########################## Q2 ##################### -->

    <?php

    # Decide o que vai aparecer no header da secção
    $q2artista = (isset($_POST['q2artista']) ? test_input($_POST['q2artista']) : '');
    $header = ($q2artista != '' ? $q2artista : '_______________');

     ?>

    <h1>Q2 - Lista de Eventos de <?php echo $header ?> em 2015.</h1>

    <!-- Formulário -->
    <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method='post'>
        Artista: <input type='text' name='q2artista'><br>
        <input type='submit'>
    </form>

    <br>

    <?php

    $Q2 = "SELECT E.enome AS 'Evento', S.nome_sala AS 'Sala', AT.data AS 'Data', C.preco AS 'Preço'
          FROM Artista AR, Evento E, Actua AT, Acontece AC, Sala S, Custo C
          WHERE YEAR(AT.data) = 2015 AND AR.nome = '$q2artista' AND AR.aid = AT.aid AND AT.eid = E.eid AND AC.eid = E.eid AND AC.sid = S.sid AND C.eid = E.eid
          ORDER BY C.preco";
    ?>

    <!--Tabela -->
    <table style='width:70%' border='1'>
    <?php
    if ($q2artista != '') {
        $results2 = $conn->query($Q2) or die ('Error, query failed');

        # Se o resultado da query estiver vazio, enviar uma mensagem ao utilizador
        if (mysqli_num_rows($results2) == 0) {
            echo 'No information';

        } else {

            # Linha inicial da tabela.
            echo '<tr>';
            $columns = $results2->fetch_fields();
                foreach($columns as $column) {
                    echo '<th><b>' . $column->name . '</br></th>';
                }
            echo '</tr>';

            # Linhas com os dados
            while ($row = $results2->fetch_array(MYSQLI_ASSOC)) {
                echo '<tr>';
                foreach($row as $key => $element) {
                    echo '<td>' . $element . '</td>';
                }
                echo '</tr>';
            }
        }
    }
    ?>
    </table>

<!-- ########################## Q6 ##################### -->

    <?php

    $q6estilo = (isset($_POST['q6estilo']) ? test_input($_POST['q6estilo']) : '');
    $header = ($q6estilo != '' ? $q6estilo : '_______________');

     ?>

    <h1>Q6 - Classificações dos Artistas de <?php echo $header ?>.</h1>

    <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method='post'>
        Estilo: <input type='text' name='q6estilo'><br>
        <input type='submit'>
    </form>

    <br>

    <?php

    $Q6 = "SELECT A.nome AS 'Artista', sum(if (GO.gptipo = 1, 1, 0)) AS 'Classificações Positivas', sum(if (GO.gptipo = 0, 1, 0)) AS 'Classificações Negativas'
           FROM Artista A, Gosto_performance GO, Genero GE
           WHERE GE.nome = '$q6estilo' AND A.code_genero = GE.code AND GO.aid = A.aid
           GROUP BY A.nome";
    ?>

    <table style='width:70%' border='1'>
    <?php
    if ($q6estilo != '') {
        $results6 = $conn->query($Q6) or die ('Error, query failed');

        if (mysqli_num_rows($results6) == 0) {
            echo 'No information';

        } else {

            echo '<tr>';
            $columns = $results6->fetch_fields();
                foreach($columns as $column) {
                    echo '<th><b>' . $column->name . '</br></th>';
                }
            echo '</tr>';

            while ($row = $results6->fetch_array(MYSQLI_ASSOC)) {
                echo '<tr>';
                foreach($row as $key => $element) {
                    echo '<td>' . $element . '</td>';
                }
                echo '</tr>';
            }
        }
    }
    ?>
    </table>

<!-- ########################## Q9 ##################### -->

    <?php


    $q9festival = (isset($_POST['q9festival']) ? test_input($_POST['q9festival']) : '');
    $q9anos = (isset($_POST['q9anos']) ? test_input($_POST['q9anos']) : '');

    $headerfestival = ($q9festival != '' ? $q9festival : '_______________');
    $headeranos = ($q9anos != '' ? $q9anos : '__');


     ?>

    <h1>Q9 - Classificações dos Artistas que tocaram no <?php echo $headerfestival ?> nos últimos <?php echo $headeranos ?> anos. </h1>

    <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method='post'>
        Festival: <input type='text' name='q9festival'><br>
        Anos: <input type='text' name='q9anos'><br>
        <input type='submit'>
    </form>

    <br>

    <?php

    $Q9 = "SELECT GE.nome AS 'Género', A.nome AS 'Artista', sum(if (GO.gptipo = 1, 1, 0)) AS 'Classificações Positivas'
           FROM Genero GE, Artista A, Gosto_performance GO
           WHERE GE.code = A.code_genero AND A.aid = GO.aid AND NOT EXISTS (SELECT E.eid
                                                                             FROM Evento E, Ocorre O
                                                                             WHERE O.eid = E.eid AND E.enome like '$q9festival%' AND
                                                                                  year(O.data) >= (2015 - ($q9anos - 1)) AND
                                                                                  E.eid NOT IN (SELECT AC.eid
                                                                                                FROM Artista AR, Actua AC
                                                                                                WHERE AR.aid = AC.aid AND AR.aid = A.aid))
                                                           AND GO.eid IN (SELECT E.eid
                                                                         FROM Evento E, Ocorre O
                                                                         WHERE O.eid = E.eid AND E.enome like '$q9festival%' AND
                                                                              year(O.data) >= (2015 - ($q9anos - 1)))
           GROUP BY GE.nome, A.nome
           ORDER BY 'Classificações Positivas'";
    ?>

    <table style='width:70%' border='1'>
    <?php
    if ($q9festival != '' and $q9anos != '') {
        $results9 = $conn->query($Q9) or die ('Error, query failed');

        if (mysqli_num_rows($results9) == 0) {
            echo 'No information.';

        } else {

            echo '<tr>';
            $columns = $results9->fetch_fields();
                foreach($columns as $column) {
                    echo '<th><b>' . $column->name . '</br></th>';
                }
            echo '</tr>';

            while ($row = $results9->fetch_array(MYSQLI_ASSOC)) {
                echo '<tr>';
                foreach($row as $key => $element) {
                    echo '<td>' . $element . '</td>';
                }
                echo '</tr>';
            }
        }
    }
    ?>
    </table>

<?php mysqli_close($conn) ?>


</body>

</html>
