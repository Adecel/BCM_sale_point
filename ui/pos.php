<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  
  ob_start();
  include_once 'connectdb.php';
  session_start();

  if(isset($_SESSION['role'])) {
    if($_SESSION['role'] == 'Admin') {
      include_once 'header.php';
    }elseif ($_SESSION['role'] == 'Manager') { 
      include_once 'ManagerHeader.php';
    }elseif ($_SESSION['role'] == 'Utilisateur') {
      include_once 'UserHeader.php';
    }else {
      header('location:../index.php');
      exit();
    }
  }else {
    header('location:../index.php');
    exit();
  }

  function fill_product($pdo){
    $output='';
    $select=$pdo->prepare("SELECT * FROM tProduct WHERE IsDeleted = 0 ORDER BY ProductId ASC");
    $select->execute();
    $result=$select->fetchAll();

    foreach($result as $row){
      $output.='<option value="'.$row["ProductId"].'">'.$row["ProductName"].'</option>';
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

    $arr_ProductId     = $_POST['ProductId_arr'];
    $arr_Barcode = $_POST['Barcode_arr'];
    $arr_name    = $_POST['ProductName_arr'];
    $arr_Stock   = $_POST['Stock_c_arr'];
    $arr_qty     = $_POST['quantity_arr'];
    $arr_price   = $_POST['price_c_arr'];
    $arr_total   = $_POST['SalePrice_arr'];

    $insert=$pdo->prepare("insert into tbl_invoice (order_date,subtotal,total,payment_type,due,paid) 
    values(:orderdate,:subtotal,:total,:payment_type,:due,:paid)");

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
      for($i=0;$i<count($arr_ProductId);$i++){
        $rem_qty=$arr_Stock[$i]-$arr_qty[$i];
        if($rem_qty<0){
          return"Order is not completed";
        }
        else{
          $update=$pdo->prepare("update tProduct SET Stock='$rem_qty' where ProductId='".$arr_ProductId[$i]."'");
          $update->execute();
        }//else end here

        $insert=$pdo->prepare("insert into tbl_invoice_details (invoice_id,barcode,product_id,product_name,qty,rate,saleprice,order_date) 
        values (:invid,:Barcode,:ProductId,:name,:qty,:rate,:SalePrice,:order_date)");
        $insert->bindParam(':invid',$invoice_id);
        $insert->bindParam(':Barcode',$arr_Barcode[$i]);
        $insert->bindParam(':ProductId',$arr_ProductId[$i]);
        $insert->bindParam(':name',$arr_name[$i]);
        $insert->bindParam(':qty',$arr_qty[$i]);
        $insert->bindParam(':rate',$arr_price[$i]);
        $insert->bindParam(':SalePrice',$arr_total[$i]);
        $insert->bindParam(':order_date',$orderdate);

        if(!$insert->execute()){
          print_r($insert->errorInfo());
        }
      }//end for loop

      header('location:OrderList.php');
    }
    
  }
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
                    <input type="text" class="form-control" placeholder="Scanner le code-barres" autocomplete="off" name="txtBarcode" id="txtBarcode_id">
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

  var ProductNamearr=[];

  $(function() {
    $('#txtBarcode_id').on('change', function() {
      var Barcode = $("#txtBarcode_id").val();
      $.ajax({
        url:"getproduct.php",
        method:"get",
        dataType: "json",
        data:{id:Barcode},

        success:function(data){
          if(jQuery.inArray(data["ProductId"],ProductNamearr)!== -1){

            var actualqty = parseInt($('#qty_id'+data["ProductId"]).val())+1;
            $('#qty_id'+data["ProductId"]).val(actualqty);

            var SalePrice=parseInt(actualqty)*data["SalePrice"];

            $('#SalePrice_id'+data["ProductId"]).html(SalePrice);
            $('#SalePrice_idd'+data["ProductId"]).val(SalePrice);

            // $("#txtBarcode_id").val("");
            calculate(0,0);

          }else{
            addrow(data["ProductId"],data["ProductName"],data["SalePrice"],data["Stock"],data["Barcode"]);
            ProductNamearr.push(data["ProductId"]);

            //$("#txtBarcode_id").val("");

            function addrow(ProductId,ProductName,SalePrice,Stock,Barcode){
              var tr='<tr>'+
              '<input type="hidden" class="form-control Barcode" name="Barcode_arr[]" id="Barcode_id'+Barcode+'" value="'+Barcode+'" >'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><class="form-control ProductName_c" name="ProductName_arr[]" <span class="badge badge-dark">'+ProductName+'</span><input type="hidden" class="form-control ProductId" name="ProductId_arr[]" value="'+ProductId+'" ><input type="hidden" class="form-control ProductName" name="ProductName_arr[]" value="'+ProductName+'" >  </td>'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary Stocklbl" name="Stock_arr[]" id="Stock_id'+ProductId+'">'+Stock+'</span><input type="hidden" class="form-control Stock_c" name="Stock_c_arr[]" id="Stock_idd'+ProductId+'" value="'+Stock+'"></td>'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id'+ProductId+'">'+SalePrice+'</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd'+ProductId+'" value="'+SalePrice+'"></td>'+

              '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id'+ProductId+'" value="'+1+'" size="1"></td>'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalamt" name="netamt_arr[]" id="SalePrice_id'+ProductId+'">'+SalePrice+'</span><input type="hidden" class="form-control SalePrice" name="SalePrice_arr[]" id="SalePrice_idd'+ProductId+'" value="'+SalePrice+'"></td>'+

              //remove button code start here

              // '<td style="text-align:left; vertical-align:middle;"><center><name="remove" class"btnremove" data-id="'+ProductId+'"><span class="fas fa-trash" style="color:red"></span></center></td>'+
              // '</tr>';

              '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="'+ProductId+'"><span class="fas fa-trash"></span></center></td>'+


              '</tr>';

              $('.details').append(tr);
              calculate(0,0);
            }//end function addrow
          }

          $("#txtBarcode_id").val("");
        }   // end of success function
      })  // end of ajax request
    })  // end of onchange function
  }); // end of main function

  var ProductNamearr=[];

  $(function() {
    $('.select2').on('change', function() {
      var Productid = $(".select2").val();
      $.ajax({
        url:"getproduct.php",
        method:"get",
        dataType: "json",
        data:{id:Productid},
        success:function(data){
          if(jQuery.inArray(data["ProductId"],ProductNamearr)!== -1){
            var actualqty = parseInt($('#qty_id'+data["ProductId"]).val())+1;
            $('#qty_id'+data["ProductId"]).val(actualqty);
            var SalePrice=parseInt(actualqty)*data["SalePrice"];
            $('#SalePrice_id'+data["ProductId"]).html(SalePrice);
            $('#SalePrice_idd'+data["ProductId"]).val(SalePrice);

            calculate(0,0);
          }else{
            addrow(data["ProductId"],data["ProductName"],data["SalePrice"],data["Stock"],data["Barcode"]);
            ProductNamearr.push(data["ProductId"]);
            function addrow(ProductId,ProductName,SalePrice,Stock,Barcode){
              var tr='<tr>'+
              '<input type="hidden" class="form-control Barcode" name="Barcode_arr[]" id="Barcode_id'+Barcode+'" value="'+Barcode+'" >'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><class="form-control ProductName_c" name="ProductName_arr[]" <span class="badge badge-dark">'+ProductName+'</span><input type="hidden" class="form-control ProductId" name="ProductId_arr[]" value="'+ProductId+'" ><input type="hidden" class="form-control ProductName" name="ProductName_arr[]" value="'+ProductName+'" >  </td>'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary Stocklbl" name="Stock_arr[]" id="Stock_id'+ProductId+'">'+Stock+'</span><input type="hidden" class="form-control Stock_c" name="Stock_c_arr[]" id="Stock_idd'+ProductId+'" value="'+Stock+'"></td>'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id'+ProductId+'">'+SalePrice+'</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd'+ProductId+'" value="'+SalePrice+'"></td>'+

              '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id'+ProductId+'" value="'+1+'" size="1"></td>'+

              '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalamt" name="netamt_arr[]" id="SalePrice_id'+ProductId+'">'+SalePrice+'</span><input type="hidden" class="form-control SalePrice" name="SalePrice_arr[]" id="SalePrice_idd'+ProductId+'" value="'+SalePrice+'"></td>'+

              //remove button code start here
              // '<td style="text-align:center; vertical-align:middle;"><center><name="remove" class"btnremove" data-id="'+ProductId+'"><span class="fas fa-trash" style="color:red"></span></center></td>'+
              '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="'+ProductId+'"><span class="fas fa-trash"></span></center></td>'+
              '</tr>';               
              $('.details').append(tr);
              calculate(0,0);

            }//end function addrow
          }
          $("#txtBarcode_id").val("");
        }   // end of success function
      })  // end of ajax request
    })  // end of onchange function
  }); // end of main function

  $("#itemtable").delegate(".qty" ,"keyup change", function(){
    var quantity=$(this);
    var tr = $(this).parent().parent();

    if((quantity.val()-0)>(tr.find(".Stock_c").val()-0)){
      Swal.fire("ALERTE!", "Cette quantité est plus que le disponible", "warning");
      quantity.val(1);

      tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
      tr.find(".SalePrice").val(quantity.val() * tr.find(".price").text());
      calculate(0,0);
    }else{
      tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
      tr.find(".SalePrice").val(quantity.val() * tr.find(".price").text());
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

    $(".SalePrice").each(function(){
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
    ProductNamearr=jQuery.grep(ProductNamearr,function(value){
      return value!=removed;
      calculate(0,0);
    })

    $(this).closest('tr').remove();
    calculate(0,0);
  });
</script>