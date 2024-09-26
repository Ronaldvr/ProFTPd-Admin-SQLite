<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 *
 */

global $cfg;

include_once ("configs/config.php");
include_once ("includes/Session.php");
include_once ("includes/AdminClass.php");

$ac = new AdminClass($cfg);


if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "export") {
  $result = $ac->ExportNewUsertoLinux();
  if (empty($errormsg)) {
    $infomsg = 'exportedsuccessfully.';
}
}

  include ("includes/header.php");
  ?>
  <?php include ("includes/messages.php"); ?>

<?php if (!empty($_REQUEST["action"]) && $_REQUEST["action"] == "export") { ?>
<!-- action: export -->
<div class="col-xs-12 col-sm-8 col-md-6 center">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
              <a class="btn btn-primary pull-right" href="users.php" role="button">View users &raquo;</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php } else { ?>
<!-- action: show -->
<div class="col-xs-12 col-sm-8 col-md-6 center">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Export users</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <form role="form" class="form-horizontal" method="post">
            <!-- GID -->
            <div class="form-group">
              <div class="col-sm-12">
                <p>Please confirm.</p>
              </div>
            </div>
            <!-- Actions -->
            <div class="form-group">
              <div class="col-sm-12">
                <a class="btn btn-default" role="group" href="users.php"<?php echo $field_id; ?>=<?php echo $id; ?>">Cancel</a>
                <button type="submit" class="btn btn-danger pull-right" role="group" name="action" value="export" <?php if (isset($errormsg)) { echo 'disabled="disabled"'; } ?>>Export users</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
