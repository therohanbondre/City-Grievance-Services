<?php 
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
  { 
header('location:index.php');
}
else{
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>CGS | Complaint History</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        
    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">

    <link href="assets/css/table-responsive.css" rel="stylesheet">

   
  </head>

  <body>

  <section id="container" >
<?php include("includes/header.php");?>
<?php include("includes/sidebar.php");?>

      <section id="main-content">
          <section class="wrapper">
          	<h3><i class="fa fa-angle-right"></i> Your Complaint History</h3>
		  		<div class="row mt">
			  		<div class="col-lg-12">
                      <div class="content-panel">
                          <?php
                          $statusCounts = array(
                              'pending' => 0,
                              'in-process' => 0,
                              'closed' => 0,
                              'other' => 0
                          );
                          $complaints = array();
                          $query = app_db_query($con, "select * from tblcomplaints where userId='" . $_SESSION['id'] . "' order by regDate desc");
                          while ($row = app_db_fetch_array($query)) {
                              $status = strtolower(trim((string) $row['status']));
                              if ($status === '' || $status === 'null') {
                                  $filterStatus = 'pending';
                              } elseif ($status === 'in process') {
                                  $filterStatus = 'in-process';
                              } elseif ($status === 'closed') {
                                  $filterStatus = 'closed';
                              } else {
                                  $filterStatus = 'other';
                              }
                              $statusCounts[$filterStatus]++;
                              $complaints[] = array('row' => $row, 'filterStatus' => $filterStatus);
                          }
                          $complaintTotal = count($complaints);
                          ?>
                          <div class="row" style="padding: 15px 15px 0;">
                              <div class="col-sm-6 form-group">
                                  <label for="complaint-search">Search complaints</label>
                                  <input type="search" id="complaint-search" class="form-control" placeholder="Complaint number, date, or status" aria-controls="complaint-history-table">
                              </div>
                              <div class="col-sm-4 form-group">
                                  <label for="complaint-status-filter">Filter by status</label>
                                  <select id="complaint-status-filter" class="form-control" aria-controls="complaint-history-table">
                                      <option value="all">All statuses (<?php echo $complaintTotal; ?>)</option>
                                      <option value="pending">Not processed (<?php echo $statusCounts['pending']; ?>)</option>
                                      <option value="in-process">In process (<?php echo $statusCounts['in-process']; ?>)</option>
                                      <option value="closed">Closed (<?php echo $statusCounts['closed']; ?>)</option>
                                      <?php if ($statusCounts['other'] > 0) { ?>
                                      <option value="other">Other status (<?php echo $statusCounts['other']; ?>)</option>
                                      <?php } ?>
                                  </select>
                              </div>
                              <div class="col-sm-2 form-group">
                                  <button type="button" id="clear-complaint-filters" class="btn btn-default" style="margin-top: 25px;">Clear filters</button>
                              </div>
                          </div>
                          <p id="complaint-results" role="status" aria-live="polite" style="padding: 0 30px;"></p>
                          <section id="unseen">
                            <div class="table-responsive">
                            <table id="complaint-history-table" class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr style="text-align: center">
                                  <th scope="col" style="text-align: center">Complaint Number</th>
                                  <th scope="col" style="text-align: center">Registration Date</th>
                                  <th scope="col" style="text-align: center">Last Updated</th>
                                  <th scope="col" style="text-align: center">Status</th>
                                  <th scope="col" style="text-align: center">Action</th>
                                  
                              </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($complaints as $complaint) {
                                  $row = $complaint['row'];
                                  $filterStatus = $complaint['filterStatus'];
                                  ?>
                              <tr data-status="<?php echo $filterStatus; ?>">
                                  <td align="center"><?php echo htmlentities($row['complaintNumber']);?></td>
                                  <td align="center"><?php echo htmlentities($row['regDate']);?></td>
                                  <td align="center"><?php echo htmlentities($row['lastUpdationDate']);?></td>
                                  <td align="center">
                                      <?php if ($filterStatus === 'pending') { ?>
                                      <span class="label label-warning">Not processed yet</span>
                                      <?php } elseif ($filterStatus === 'in-process') { ?>
                                      <span class="label label-info">In process</span>
                                      <?php } elseif ($filterStatus === 'closed') { ?>
                                      <span class="label label-success">Closed</span>
                                      <?php } else { ?>
                                      <span class="label label-default"><?php echo htmlentities($row['status']);?></span>
                                      <?php } ?>
                                  </td>
                                  <td align="center">
                                      <a class="btn btn-primary" href="complaint-details.php?cid=<?php echo rawurlencode($row['complaintNumber']);?>">View Details</a>
                                  </td>
                              </tr>
                              <?php } ?>
                              <tr id="no-matching-complaints" hidden>
                                  <td colspan="5" class="text-center">No complaints match these filters.</td>
                              </tr>
                              <?php if ($complaintTotal === 0) { ?>
                              <tr id="no-complaints">
                                  <td colspan="5" class="text-center">You have not submitted any complaints yet. <a href="register-complaint.php">Lodge a complaint</a>.</td>
                              </tr>
                              <?php } ?>
                              </tbody>
                          </table>
                            </div>
                          </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->			
		  	</div><!-- /row -->
		  	
		  	

		</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->
<?php include("includes/footer.php");?>
  </section>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>


    <!--common script for all pages-->
    <script src="assets/js/common-scripts.js"></script>

    <script src="assets/js/complaint-history.js"></script>

  </body>
</html>
<?php } ?>
