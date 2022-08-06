<!--edit/add checklist-->
<div id="element-checklist-text" class="hidden">
    <textarea class="form-control form-control-sm checklist_text" rows="3" name="checklist_text"
        id="checklist_text"></textarea>
    <div class="text-right">
        <button type="button" class="btn btn-default  btn-xs  js-card-checklist-toggle" data-toggle="close"
            href="JavaScript:void(0);">
            {{ cleanLang(__('lang.close')) }}
        </button>
        <button type="button" class="btn btn-danger  btn-xs js-ajax-ux-request x-submit-button disable-on-click"
            id="checklist-submit-button" data-url="" data-type="form" data-ajax-type="post"
            data-form-id="element-checklist-text" data-loading-target="element-checklist-text">
             {{ cleanLang(__('lang.add')) }}
        </button>
        <input type="hidden" name="checklist-id" id="checklist-id" value="">
    </div>
</div>


<!--popover-->
<div class="popover card-popover hidden" role="tooltip" id="card-popover">
    <div class="popover-header"></div>
    <div class="popover-body"></div>
</div>
