<div class="setup-inner-steps setup-requirements">

    <h5 class="text-info"> Admin User Details </h5>
    <form class="form-horizontal form-material" id="setupForm" name="setupForm">
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">First Name</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="text" value="" id="first_name" name="first_name">
            </div>
        </div>
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">Last Name</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="text" value="" id="last_name" name="last_name">
            </div>
        </div>
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">Email Address</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="text" value="" id="email" name="email">
            </div>
        </div>
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">Password</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="password" value="" id="password" name="password">
            </div>
        </div>

        <!--continue-->
        <!--continue-->
        <div class="x-button text-right p-t-20">
            <button class="btn waves-effect waves-light btn-info btn-extra-padding" data-button-loading-annimation="yes"
                data-button-disable-on-click="yes" data-type="form" data-ajax-type="post" data-form-id="setupForm"
                id="continueButton" type="submit" data-url="{{url('setup/adminuser') }}">Continue</button>
        </div>
    </form>
</div>