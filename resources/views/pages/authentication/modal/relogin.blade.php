<!--modal-->
<div class="modal" role="dialog" aria-labelledby="reloginModal" id="reloginModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" id="reloginModalContainer">
        <form class="form-material form-horizontal" id="reloginForm">
        <div class="modal-content">
            <div class="modal-body relogin" id="reloginModalBody">

                <div class="splash-image">
                    <img src="{{ url('/') }}/public/images/relogin.png" alt="404 - Not found" />
                </div>
                <div class="splash-text">
                    {{ cleanLang(__('lang.session_timed_out_login')) }}
                </div>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control form-control-sm" id="email" name="email"
                            placeholder="{{ cleanLang(__('lang.email')) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <input type="password" class="form-control form-control-sm" id="password" name="password"
                            placeholder="{{ cleanLang(__('lang.password')) }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="reloginModalButton" class="btn btn-rounded btn-info waves-effect text-left"
                    data-url="{{ url('/login?action=relogin') }}" data-loading-target="reloginModalBody" data-ajax-type="POST"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.log_in')) }}</button>
            </div>
        </div>
    </form>
    </div>
</div>