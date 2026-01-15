<?php
define("TRYS", 3);


if (isset($_GET['skaicius']) && is_numeric($_GET['skaicius'])) {
    
    $skaicius = $_GET['skaicius']; 

    if ($skaicius > 0) {
        echo "Skaicius: " . $skaicius . "<br>";
        echo "Faktorialas: " . skaiciuoti($skaicius) . "<br><br>";

        $pirm = $skaicius;
        $iter = 0;

        while ($pirm != 1) {
            if ($pirm % 2 == 0) {
                $pirm = $pirm / 2;
            } else {
                $pirm = TRYS * $pirm + 1;
            }
            echo $pirm . " ";
            $iter++;
        }

        echo "<br><br>Iteraciju kiekis: $iter";
    } else {
        echo "Skaičius turi būti didesnis už 0.";
    }

} else {
    echo "Iveskite skaiciu per GET parametra, pvz.: ?skaicius=10";
}

function skaiciuoti($n) {
    $rez = 1;
    for ($i = 1; $i <= $n; $i++) {
        $rez *= $i;
    }
    return $rez;
}
?>