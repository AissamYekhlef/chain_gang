<?php require_once('../private/initialize.php'); ?>
<!-- $database mysqli object -->
<?php $page_title = 'Inventory'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<?php

// get the data by offset 
$current_page =  $_GET['page'] ?? 1; //php > 7
$per_page = 5;
$total_count = Bicycle::count_all();
$pagination = new Pagination($current_page, $per_page, $total_count);


$sql = $pagination->pagination_offset($per_page, $pagination->offset(), Bicycle::table_name());

$bicycles = Bicycle::find_by_sql($sql);
  
?>

<div id="main">

  <div id="page">
    <div class="intro">
      <img class="inset" src="<?php echo url_for('/images/AdobeStock_55807979_thumb.jpeg') ?>" />
      <h2>Our Inventory of Used Bicycles</h2>
      <p>Choose the bike you love.</p>
      <p>We will deliver it to your door and let you try it before you buy it.</p>
    </div>

    <table id="inventory">
      <tr>
        <th>ID</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Year</th>
        <th>Category</th>
        <th>Gender</th>
        <th>Color</th>
        <th>Price</th>
        <th></th>
      </tr>

<?php 
  // echo "Get data from CSV file";  
  // $parser = new ParseCSV(DATABASE_PATH . 'used_bicycles.csv');
  // $bike_array = $parser->parse();
  // echo "<div style='text-align:center; margin:20px'> the Bicycles count : " ;
  // echo $parser->row_count() ;
  // echo  "</div>";

  // echo "<br>Get all data from database"; 
  // $bike_array = Bicycle::find_all();

?>
     
    <?php foreach ($bicycles as $bike){?>
    <?php 
      //  $bike = new Bicycle($args); ?>
      <tr>
        <td> <?php echo $bike->id ?> </td>
        <td> <?php echo $bike->brand ?> </td>
        <td> <?php echo $bike->model ?> </td>
        <td> <?php echo $bike->year ?> </td>
        <td> <?php echo $bike->category ?> </td>
        <td> <?php echo $bike->gender ?> </td>
        <td> <?php echo $bike->color ?> </td>
        <td> <?php echo '$' . number_format( $bike->price, 2); ?> </td>
        <td><a href="detail.php?id=<?php echo $bike->id?>" . >View</a></td>
      </tr>
    <?php }?>
    
    </table>

      <?php
        $url = url_for("bicycles.php");
        echo $pagination->page_links($url);
      ?>

  </div>

<?php
  // $result = Bicycle::find_all();
  // $row = $result->fetch_assoc();

  // // print_r($result->fetch_array());
  // echo "Affected_rows: " . $database->affected_rows;
  // echo "  row: " . $row['brand'];
  // $row = $result->fetch_assoc();
  // echo "  row: " . $row['brand'];

  // $num = $result->num_rows;
  // echo "  num_row: " . $num;



  // $result->free();
  
?>

</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
