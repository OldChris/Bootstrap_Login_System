<?php
function fruitoftheday()
{
  
      $conn = mysqli_connect($GLOBALS['AppDbHost'], $GLOBALS['AppDbUsername'], $GLOBALS['AppDbPassword'], $GLOBALS['AppDbName']);
      // Check connection
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }
      $randomId=random_int(1,7);
      $sql = "SELECT `id`, `name`, `price` FROM `fruit` Where id=$randomId";
      $result = $conn->query($sql);
      
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          if ( strpos("AEIOU",substr($row['name'],0,1)) !== false )
          {
            $ana="an ";
          }
          else
          {
            $ana="a ";
          }
          echo "The fruit of the day is " . $ana .  $row["name"] . "<br>";
        }
      } else {
        echo "0 results";
      }
      $conn->close();
  return true;
}

function show_fruit()
{
    return true;
}

function show_all_fruit()
{
  $conn = mysqli_connect($GLOBALS['AppDbHost'], $GLOBALS['AppDbUsername'], $GLOBALS['AppDbPassword'], $GLOBALS['AppDbName']);
  // Check connection
        if (!$conn) {
          die("Connection failed: " . mysqli_connect_error());
        }
    
        $sql = "SELECT `id`, `name`, `price` FROM `fruit`";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {

          while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Name: " . $row["name"]. " " . $row["price"]. "<br>";
          }
        } else {
          echo "0 results";
        }
        $conn->close();
    return true;
}

function show_apples()
{
    
  $conn = mysqli_connect($GLOBALS['AppDbHost'], $GLOBALS['AppDbUsername'], $GLOBALS['AppDbPassword'], $GLOBALS['AppDbName']);
  // Check connection
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT `id`, `name`, `price` FROM `fruit` WHERE `name` LIKE '%Apple%'";

  //  $sql = "SELECT * FROM `fruit` WHERE 1";
   // echo "query = " . $sql;
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. " " . $row["price"]. "<br>";
      }
    } else {
      echo "0 results";
    }
    $conn->close();
    return true;
}

function show_40_apples()
{
    echo '<div class="starter-template text-center py-3 px-3">' . PHP_EOL;
    echo '<h1>Here are 40 apples</h1>' . PHP_EOL;
    for ($i=0;$i<40;$i++)
    {
        echo '<p>  Apple number ' . strval( $i +1). '</p>';
    }
    echo '</div>' . PHP_EOL; 
    return true;
}



?>