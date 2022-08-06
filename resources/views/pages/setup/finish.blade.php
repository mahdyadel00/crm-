<div class="setup-welcome">

    <!--image-->
    <div class="x-image">
        <img src="public/images/success.png">
    </div>

    <!--title-->
    <div class="x-title">
        <h2>Congratulations!!</h2>
    </div>

    <div class="x-subtitle">
        Your setup is now complete. You can now start using your application.
    </div>

    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
        <h3 class="text-warning"><i class="fa fa-exclamation-triangle"></i> One Last Step!</h3> 
        You must now setup a <strong>CronJob</strong>. This is usuallay done inside your web hosting provider's control panel <strong>(e.g. Cpanel)</strong>. See documentation for detailed instructions.
    </div>

    <div class="cronjob">
        <label class="col-12 control-label col-form-label">CronJob</label>
        <input class="col-12 form-control form-control-sm" type="text" value="{{ $cronjob_path }}" disabled>
    </div>


    <div class="x-button m-t-20">
        <a href="{{ url('/login') }}" class="btn waves-effect waves-light btn-block btn-info">Go To My Dashboard</a>
    </div>


</div>