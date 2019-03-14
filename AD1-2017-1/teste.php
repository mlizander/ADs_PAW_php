<?php
function f($a, $b) {
   echo "<table border=1><tr>";
   $tmp = array();

   foreach ($a as $x =>$y) {
      echo "<td>$x</td>";
      $tmp [$x] = 0;
   }

   foreach ($b as $z) {
      echo "</tr><tr>";
      $c = $tmp;
      for ($i = 0; $i <strlen($z); $i++) {
         if (key_exists($z[$i],$c)) $c[$z[$i]]++;

      }

      foreach ($a as $x => $y) {
          echo "<td>";
          if ($c[$x]) echo $y*$c[$x];
         echo "</td>";
       }
   }

   echo "</tr></table>";
   }


f(array("a"=>10,"b"=>5,"c"=>20),array("abc","adefccc","fbbdaa","cbbbbaa"));
?>