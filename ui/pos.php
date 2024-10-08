<?php
  ob_start();
  include_once 'connectdb.php';
  session_start();

// Check if the session 'role' is set, and load the appropriate header based on the role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include_once 'header.php';
    } elseif ($_SESSION['role'] == 'Manager') {
        include_once 'ManagerHeader.php';
    } elseif ($_SESSION['role'] == 'Utilisateur') {
        include_once 'UserHeader.php';
    } else {
        // Redirect to the login page or access denied page if role doesn't match
        header('location:../index.php');
        exit();
    }
} else {
    // If session 'role' is not set, redirect to the login page
    header('location:../index.php');
    exit();
}

  function fill_product($pdo){
    $output='';
    $select=$pdo->prepare("select * from tbl_product order by product asc");

    $select->execute();

    $result=$select->fetchAll();

    foreach($result as $row){
      $output.='<option value="'.$row["pid"].'">'.$row["product"].'</option>';
    }
    return $output;
  }

  if(isset($_POST['btnsaveorder'])){
    $orderdate     = date('Y-m-d');
    $subtotal      = $_POST['txtsubtotal'];
    // $discount      = $_POST['txtdiscount'];
    // $sgst          = $_POST['txtsgst'];
    // $cgst          =$_POST['txtcgst'];
    $total         = $_POST['txttotal'];
    $payment_type  =$_POST['rb'];
    $due           = $_POST['txtdue'];
    $paid          =$_POST['txtpaid'];

    $arr_pid     = $_POST['pid_arr'];
    $arr_barcode = $_POST['barcode_arr'];
    $arr_name    = $_POST['product_arr'];
    $arr_stock   = $_POST['stock_c_arr'];
    $arr_qty     = $_POST['quantity_arr'];
    $arr_price   = $_POST['price_c_arr'];
    $arr_total   = $_POST['saleprice_arr'];

    $insert=$pdo->prepare("insert into tbl_invoice (order_date,subtotal,total,payment_type,due,paid) values(:orderdate,:subtotal,:total,:payment_type,:due,:paid)");

    $insert->bindParam(':orderdate',$orderdate);
    $insert->bindParam(':subtotal',$subtotal);
    // $insert->bindParam(':discount',$discount);
    // $insert->bindParam(':sgst',$sgst);
    // $insert->bindParam(':cgst',$cgst);
    $insert->bindParam(':total',$total);
    $insert->bindParam(':payment_type',$payment_type);
    $insert->bindParam(':due',$due);
    $insert->bindParam(':paid',$paid);

    $insert->execute();

    $invoice_id=$pdo->lastInsertId();

    if($invoice_id!=null){
      for($i=0;$i<count($arr_pid);$i++){
        $rem_qty=$arr_stock[$i]-$arr_qty[$i];
        if($rem_qty<0){
          return"Order is not completed";
        }
        else{
          $update=$pdo->prepare("update tbl_product SET stock='$rem_qty' where pid='".$arr_pid[$i]."'");
          $update->execute();
        }//else end here

        $insert=$pdo->prepare("insert into tbl_invoice_details (invoice_id,barcode,product_id,product_name,qty,rate,saleprice,order_date) values (:invid,:barcode,:pid,:name,:qty,:rate,:saleprice,:order_date)");
        $insert->bindParam(':invid',$invoice_id);
        $insert->bindParam(':barcode',$arr_barcode[$i]);
        $insert->bindParam(':pid',$arr_pid[$i]);
        $insert->bindParam(':name',$arr_name[$i]);
        $insert->bindParam(':qty',$arr_qty[$i]);
        $insert->bindParam(':rate',$arr_price[$i]);
        $insert->bindParam(':saleprice',$arr_total[$i]);
        $insert->bindParam(':order_date',$orderdate);

        if(!$insert->execute()){
          print_r($insert->errorInfo());
        }
      }//end for loop

      header('location:OrderList.php');
    }//1st if end

  var_dump($arr_total);
  }

//  if($_SESSION['role']=="Admin"){
//    include_once'header.php';
//  }
//  else{
//    include_once 'UserHeader.php';
//  }

  ob_end_flush();

  $select=$pdo->prepare("select * from tbl_taxdis where taxdis_id =1");
  $select->execute();
  $row=$select->fetch(PDO::FETCH_OBJ);

?>


<style type="text/css">

  .tableFixHead{
    overflow: scroll;
    height: 520px;
  }
  .tableFixHead thead th {
    position: sticky;
    top:0 ;
    z-index: 1;
  }

  table {border-collapse:collapse; width: 100px;}
  th,td {padding:8px 16px;}
  th{background: #eee;}

</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1 class="m-0">Point Of Sale</h1> -->
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Starter Page</li> -->
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="m-0">Points de vente</h5>
            </div>

            <div class="card-body">
              <div class="row">
                <!-- section n.1 -->
                <div class="col-md-8">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Scanner le code-barres" autocomplete="off" name="txtbarcode" id="txtbarcode_id">
                  </div>

                  <form action="" method="post" name="">

              
                    <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;">
                      <option>Sélectionner ou Rechercher</option><?php echo fill_product($pdo);?>
                    </select>
                    </br>
                    <div class="tableFixHead">
                      <table id="producttable" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Produit</th>
                            <th>Stock  </th>
                            <th>Prix  </th>
                            <th>Quantité</th>
                            <th>Total  </th> 
                            <th>Enlever</th>   
                          </tr>
                        </thead>

                        <tbody class="details" id="itemtable">
                          <tr data-widget="expandable-table" aria-expanded="false"></tr>           
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- Section n.2 -->
                  <div class="col-md-4">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">TOTAL</span>
                      </div>
                      <input type="text" class="form-control" name="txtsubtotal"  id="txtsubtotal_id" readonly >
                      <div class="input-group-append">
                        <span class="input-group-text">frcs</span>
                      </div>
                    </div>

                    <hr style="height:2px; border-width:0; color:black; background-color:black;">

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">TOTAL GENERAL</span>
                      </div>
                      <input type="text" class="form-control form-control-lg total" name="txttotal" id="txttotal" readonly >
                      <div class="input-group-append">
                        <span class="input-group-text">frcs</span>
                      </div>
                    </div> 

                    <hr style="height:2px; border-width:0; color:black; background-color:black;">

                    <div class="icheck-success d-inline">
                      <input type="radio" name="rb" value="Cash" checked id="radioSuccess1">
                      <label for="radioSuccess1">
                        ESPÈCES
                      </label>
                    </div>
                    <div class="icheck-primary d-inline">
                      <input type="radio" name="rb" value="Card" id="radioSuccess2">
                      <label for="radioSuccess2">
                      CARTE
                      </label>
                    </div>
                    <div class="icheck-danger d-inline">
                      <input type="radio" name="rb" value="Check" id="radioSuccess3">
                      <label for="radioSuccess3">
                        CHEQUE
                      </label>
                    </div>

                    <hr style="height:2px; border-width:0; color:black; background-color:black;">

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Dû (qui convient)</span>
                      </div>
                      <input type="text" class="form-control" name="txtdue" id="txtdue" readonly >
                      <div class="input-group-append">
                        <span class="input-group-text">frcs</span>
                      </div>
                    </div>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">PAYÉ</span>
                      </div>
                      <input type="text" class="form-control"  name="txtpaid" id="txtpaid">
                      <div class="input-group-append">
                        <span class="input-group-text">frcs</span>
                      </div>
                    </div>

                    <hr style="height:2px; border-width:0; color:black; background-color:black;">

                    <div class="card-footer">
                      <div class="text-center">
                        <button type="submit" class="btn btn-success" name="btnsaveorder">Enregistrer la commande</button>
                      </div>
                    </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  <!-- /.col-md-6 -->
  </div>
<!-- /.row -->
</div><!-- /.container-fluid -->
</div>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
  include_once "footer.php";
?>

<script>
 
  //Initialize Select2 Elements
  $('.select2').select2()

  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })  

  var productarr=[];

  $(function() {

  $('#txtbarcode_id').on('change', function() {
    var barcode = $("#txtbarcode_id").val();
    $.ajax({
    url:"getproduct.php",
    method:"get",
    dataType: "json",
    data:{id:barcode},
    success:function(data){
    //alert("pid");

    //console.log(data);

    if(jQuery.inArray(data["pid"],productarr)!== -1){

      var actualqty = parseInt($('#qty_id'+data["pid"]).val())+1;
      $('#qty_id'+data["pid"]).val(actualqty);

      var saleprice=parseInt(actualqty)*data["saleprice"];

      $('#saleprice_id'+data["pid"]).html(saleprice);
      $('#saleprice_idd'+data["pid"]).val(saleprice);

      // $("#txtbarcode_id").val("");
      calculate(0,0);

    }else{

      addrow(data["pid"],data["product"],data["saleprice"],data["stock"],data["barcode"]);

      productarr.push(data["pid"]);

      //$("#txtbarcode_id").val("");

      function addrow(pid,product,saleprice,stock,barcode){

      var tr='<tr>'+

      '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id'+barcode+'" value="'+barcode+'" >'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><class="form-control product_c" name="product_arr[]" <span class="badge badge-dark">'+product+'</span><input type="hidden" class="form-control pid" name="pid_arr[]" value="'+pid+'" ><input type="hidden" class="form-control product" name="product_arr[]" value="'+product+'" >  </td>'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id'+pid+'">'+stock+'</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd'+pid+'" value="'+stock+'"></td>'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id'+pid+'">'+saleprice+'</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd'+pid+'" value="'+saleprice+'"></td>'+

      '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id'+pid+'" value="'+1+'" size="1"></td>'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalamt" name="netamt_arr[]" id="saleprice_id'+pid+'">'+saleprice+'</span><input type="hidden" class="form-control saleprice" name="saleprice_arr[]" id="saleprice_idd'+pid+'" value="'+saleprice+'"></td>'+

      //remove button code start here

      // '<td style="text-align:left; vertical-align:middle;"><center><name="remove" class"btnremove" data-id="'+pid+'"><span class="fas fa-trash" style="color:red"></span></center></td>'+
      // '</tr>';

      '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="'+pid+'"><span class="fas fa-trash"></span></center></td>'+


      '</tr>';

      $('.details').append(tr);
      calculate(0,0);
      }//end function addrow
    }
    $("#txtbarcode_id").val("");
    }   // end of success function
    })  // end of ajax request
    })  // end of onchange function
    }); // end of main function

    var productarr=[];

    $(function() {

    $('.select2').on('change', function() {


    var productid = $(".select2").val();

    $.ajax({
    url:"getproduct.php",
    method:"get",
    dataType: "json",
    data:{id:productid},
    success:function(data){
    //alert("pid");

    //console.log(data);

    if(jQuery.inArray(data["pid"],productarr)!== -1){

      var actualqty = parseInt($('#qty_id'+data["pid"]).val())+1;
      $('#qty_id'+data["pid"]).val(actualqty);

      var saleprice=parseInt(actualqty)*data["saleprice"];

      $('#saleprice_id'+data["pid"]).html(saleprice);
      $('#saleprice_idd'+data["pid"]).val(saleprice);

      //$("#txtbarcode_id").val("");

      calculate(0,0);
    }else{

      addrow(data["pid"],data["product"],data["saleprice"],data["stock"],data["barcode"]);

      productarr.push(data["pid"]);

      //$("#txtbarcode_id").val("");

      function addrow(pid,product,saleprice,stock,barcode){

      var tr='<tr>'+
      '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id'+barcode+'" value="'+barcode+'" >'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><class="form-control product_c" name="product_arr[]" <span class="badge badge-dark">'+product+'</span><input type="hidden" class="form-control pid" name="pid_arr[]" value="'+pid+'" ><input type="hidden" class="form-control product" name="product_arr[]" value="'+product+'" >  </td>'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id'+pid+'">'+stock+'</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd'+pid+'" value="'+stock+'"></td>'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id'+pid+'">'+saleprice+'</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd'+pid+'" value="'+saleprice+'"></td>'+

      '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id'+pid+'" value="'+1+'" size="1"></td>'+

      '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalamt" name="netamt_arr[]" id="saleprice_id'+pid+'">'+saleprice+'</span><input type="hidden" class="form-control saleprice" name="saleprice_arr[]" id="saleprice_idd'+pid+'" value="'+saleprice+'"></td>'+

      //remove button code start here

      // '<td style="text-align:center; vertical-align:middle;"><center><name="remove" class"btnremove" data-id="'+pid+'"><span class="fas fa-trash" style="color:red"></span></center></td>'+

      '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="'+pid+'"><span class="fas fa-trash"></span></center></td>'+


      '</tr>';

                      
      $('.details').append(tr);
      calculate(0,0);

      }//end function addrow
    }
    $("#txtbarcode_id").val("");
    }   // end of success function
    })  // end of ajax request
    })  // end of onchange function
    }); // end of main function

    $("#itemtable").delegate(".qty" ,"keyup change", function(){

    var quantity=$(this);
    var tr = $(this).parent().parent();

    if((quantity.val()-0)>(tr.find(".stock_c").val()-0)){

    Swal.fire("ALERTE!", "Cette quantité est plus que le disponible", "warning");
    quantity.val(1);

    tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());

    tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
    calculate(0,0);
    }else{
    tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());

    tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
    calculate(0,0);
    }
    });

    function calculate(dis,paid){

    var subtotal=0;
    var discount=dis;
    var sgst=0;
    var cgst=0;
    var total=0;
    var paid_amt=paid;
    var due=0;

$(".saleprice").each(function(){

  subtotal=subtotal+($(this).val()*1);
});

$("#txtsubtotal_id").val(subtotal.toFixed(2));

sgst=parseFloat($("#txtsgst_id_p").val());

cgst=parseFloat($("#txtcgst_id_p").val());

discount=parseFloat($("#txtdiscount_p").val());

sgst=sgst/100;
sgst=sgst*subtotal;

cgst=cgst/100;
cgst=cgst*subtotal;

discount=discount/100;
discount=discount*subtotal;

$("#txtsgst_id_n").val(sgst.toFixed(2));

$("#txtcgst_id_n").val(cgst.toFixed(2));

$("#txtdiscount_n").val(discount.toFixed(2));


// total=sgst+cgst+subtotal-discount;
total = subtotal;
due=total-paid_amt;


$("#txttotal").val(total.toFixed(2));

$("#txtdue").val(due.toFixed(2));

}  //end calculate function


$("#txtdiscount_p").keyup(function(){

var discount=$(this).val();

calculate(discount,0);

});

$("#txtpaid").keyup(function(){

var paid=$(this).val();
var discount=$("#txtdiscount_p").val();
calculate(discount,paid);

});


$(document).on('click','.btnremove',function(){

var removed=$(this).attr("data-id");
productarr=jQuery.grep(productarr,function(value){

  return value!=removed;
  calculate(0,0);
})

$(this).closest('tr').remove();
calculate(0,0);
});
</script>