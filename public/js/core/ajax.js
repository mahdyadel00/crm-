"use strict";

/**------------------------------------------------------------------------
 * writes console.log output
 *------------------------------------------------------------------------*/
NX.log = function (data1, data2) {

	if (data1 != undefined) {
		console.log(data1);
	}
	if (data2 != undefined) {
		console.log(data2);
	}
};


/**------------------------------------------------------------------------
 * [returns] - unique id
 *------------------------------------------------------------------------*/
NX.uniqueID = function () {
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
		var r = Math.random() * 16 | 0,
			v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(16);
	});
}


/**--------------------------------------------------------------------------------------------------------------------------------------
 * DISPLAY NOTIFICATIONS TO END USER
 * - You can edit this notifications function to suit the notification system being used by the main application *
 *
 * [PARAMS] - object
 *
 * [REQUIRED PARAM]
 *        obj['message']
 *
 * [OPTIONAL - FOR NOTY]
 *       obj['type']        - success|warning
 *       obj['position']    - top|topLeft|topCenter|topRight|center|centerLeft|centerRight|bottom|bottomLeft|bottomCenter|bottomRight
 *--------------------------------------------------------------------------------------------------------------------------------------*/

NX.notification = function (obj) {

	"use strict";

	//state
	var state = false;

	//validate required
	if (typeof obj.message == 'undefined' || obj.message == '') {
		NX.log('notification() - no message was provided with notification request - exiting');
		return;
	}

	//check if we have noty
	if (typeof noty === 'function') {

		//default duration
		var duration = 3000;

		var position = (typeof NX.notification_position != 'undefined' && NX.notification_position != '') ? NX.notification_position : 'bottomLeft';
		var error_duration = (typeof NX.notification_error_duration != 'undefined' && NX.notification_error_duration != '') ? NX.notification_error_duration : duration;
		var success_duration = (typeof NX.notification_success_duration != 'undefined' && NX.notification_success_duration != '') ? NX.notification_success_duration : duration;
		var type = (typeof obj.type != 'undefined' && obj.type == 'success') ? 'success' : 'warning';

		//short display time for success message
		if (type == 'success') {
			duration = success_duration;
		}
		if (type == 'warning') {
			duration = error_duration;
		}

		//hide any existing
		$(".noty_bar").remove();

		//display noty message
		noty({
			text: obj.message,
			layout: position,
			type: type,
			timeout: duration,
			progressBar: false,
			closeWith: ['click', 'button', 'backdrop'],
		});

		//finished
		return;
	}

};




/** ---------------------------------------------------------------------------------------------AJAX UX---------------------------------------------------------*/
/**-----------------------------------------------------------------------------------------------------------------------------
 * [NAME-SPACE]: NXAJAX
 *
 * [LAST-REVISED] 01 Octomer 2019
 *
 * [REQUIRES] - In this order
 *            - [nextloop.core.js]
 *            - [nextloop.toolbox.js]
 *            - [nprogress.js] - https://github.com/rstacruz/nprogress
 *
 *
 *-------------------------------------------------------------------------------------------------------------------------------
 *
 *  [ACTIONS]
 *            - [css-triggered] : .js-ajax-ux-request | .ajax-request | .js-ajax-request
 *            - [manually-triggered] : nxAjaxUxRequest($(this));
 *            - [manually-triggered-post] : nxAjaxUxRequest($(this), optionalPostArray);
 ==================================================================================================================================
	[MAKING REQUEST WITH VIRTUAL DOM]
	
 						  $obj = $('<div>', {
 						        attr: {
 						            'data-url': 'http://dashboard.com/create/user',
 						            'data-progress-bar': 'hidden'
 						        }
  						   });
 						   nxAjaxUxRequest($obj);
====================================================================================================================================	
 *
 *          [DATA-REQUIRED]
 *          -------------------
 * 
 *            action url
 *            - [data-url]                                    url of the ajax request
 * 
 *
 *          [DATA-OPTIONAL]
 *          --------------------
 * 
 *            result target
 *            - ['data-results-target']                         [#id] of containter where results will be added
 *            - ['data-results-placement']                      will the resulsts be [append] or [replace] within the container
 *
 *            Infinite Scrolling
 *            - ['data-infinite-scroll-marker']                  [id] if applicable, the div used to track screen position for autoloading
 * 
 *            ajax method
 *            - [data-ajax-type]                                  [GET|POST|DELETE|PUT] - default type is GET. If you want to post a form
 *
 *            loading annimations
 *            -[data-button-loading-annimation="yes"] add loading annimation to clicked button (css: button-loading-annimation)
 *            -[data-button-disable-on-click="yes"] disable clicked button
 *            -[data-loading-target="main-body"] the id name only of the taret element
 *            -[data-loading-class="loading"] #create this class in your css file (loading | loading-before | loading-before-centre)
 *            -[data-loading-class="loading-placeholder"] #useful if you want to add loading as if itw as placeholder content (positioned inline)
 * 
 *           Hide Progress bar
 *           - [data-progress-bar='hidden']                      if set to 'hidden' the progress bar will not be shown. default is 'show'
 * 
 *           Do not show notification popup (regardless of whether the backend has sent one)
 *           -[data-notifications="disabled"]
 *
 *            loading overlays
 *            - [data-loading-overlay-target="main-body"] the id name only of the taret element
 *            - [data-loading-overlay-classname="overlay"] #create this class in your css file
 * 
 *            callback function (executed after ajax completed)
 *                - function names cannot be scoped, like NX.someFunction, but rather a plain function like NXsomeFunction
 *                - callback functions can also be specified in tje json resposne
 *           - [data-postrun-functions="foofunction,barfunction"] no spaces between comma
 * 
 *           [on start actions]
 *            -[data-onstart-hide="#some-element"] an element or a class to hide when the request starts
 *            -[data-onstart-show="#some-element"] an element or a class to show when the request starts
 * 
 *           Reset loading target
 *           data-reset-loading-target="true" (default is false)
 * 
 *           Modal Footer (hide or show)
 *           data-footer-visibility="hidden"
 *
 *-------------------------------------------------------------------------------------------------------------------------------
 *
 *   [STANDARD-FORM-POST]
 *   You can also send data contained in a form that the button is not a part of. Infact, it does not even have to be a form
 *   but can be a div with form elements.
 *         - The buttom must have the following attributes
 *                  - [data-type="form"]
 *                  - [data-form-id="some-form-id"]
 *                  - [data-ajax-type="post"]
 *                  - [data-url="foo.com"]
 *         - All the [form-input] elements will be sent as post data with the request
 * 
 *   [DIRECT-FORM-POST]
 *   If post data is being sent to the backend, it can be passed [directly] as an array (e.g. ['name':'fred', 'surname':'marks'] )
 *           [example][optionalPostArray]
 *           var optionalPostArray = ['name':'fred', 'surname':'marks'];
 *           nxAjaxUxRequest($(this), optionalPostArray);
 * 
 *   [RADIO BUTTONS]
 *   The current version of this module does not properly support post values for radio check boxes. Below is a workaround
 *   You create a hidden field with the real name of the field value. The value for this field will be set either on click of
 *   submit button, or inside the valicator like this
 * 
 *      <input type="hidden" name="leadstatus_color" id="leadstatus_color" value=""> 
 * 
        //validation
        $("#commonModalForm").validate({
            rules: {
                leadstatus_title: "required"
            },
            submitHandler: function (form) {

                //set selector color
                var color = $("input[name='leadstatus_color_radio']:checked").val();
                $("#leadstatus_color").val(color);
                //ajax request
                nxAjaxUxRequest($("#commonModalSubmitButton"));
            }
        });
 *
 * 
 *
 * ---------------------------------------------------------------------------------------------------------------------------------
 * [BACKEND-JSON-RESPONSE] [EXAMPLES]

        [dom_attributes]
            $jsondata['dom_attributes'][] = [
                'selector' => '#js-sorting-id',
                'attr' => 'data-url',
                'value' => 'foobar',
			];

   ----------------------------------------------------------	

        [dom_attributes]
            $jsondata['dom_html'][] = [
                'selector' => '#ajax-project-tab-body',
                'action' => 'replace',
                'value' => $html,
			];

   ----------------------------------------------------------			

        [dom_classes]	
            //[action options] add|remove
            $jsondata['dom_classes'][] = [
                'selector' => '#main-table',
                'action' => 'add',
                'value' => 'some-class-name',
			];

   ----------------------------------------------------------			

        [next_url]	
			$jsondata['next_url'] = 'http://foo';"

   ----------------------------------------------------------			

        [redirect_url]	
			$jsondata['redirect_url'] = 'http://foo';"

   ----------------------------------------------------------			

        [more_results]	
			$jsondata['more_results'] = 1; //1|0"
			
   ----------------------------------------------------------			

        [notification]	
            //[type options] error|success
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => 'request could not be completed',
			];
			
   ----------------------------------------------------------			

       [dom_visibility]	
           // show | hide | slideup | slideup-slow | slidedown | slidedown-slow
           // fadeout |fadeout-slow | slideup-remove | slideup-slow-remove
           // fadeout-remove | fadeout-slow-remove | hide-remove | close-modal
		   $jsondata['dom_visibility'][] = [
			'selector' => '#some-itme',
			'action' => 'slideout-slow',
		   ];
	

       [response]	
		   return response()->json($jsondata);

  ----------------------------------------------------------			

        [tinymce_reset]	
            //reset tinymce editors by id
            $jsondata['tinymce_reset'][] = [
                'selector' => 'some-editor', //no hash sign
			];
		   
/**------------------------------------------------------------------------
 * [GET RESULST FROM BACKEND]
 * ------------------------------------------------------------------------*/
function nxAjaxUxRequest(obj) {

	//Nextloop Namespace
	var NXAJAX = (typeof NXAJAX == 'undefined') ? {} : NXAJAX;

	NXAJAX.OBJ = obj;

	//DEBUG MODE - CONSOLE DEBUG OUTPUT
	NXAJAX.debug_mode = (typeof js_debug_mode != 'undefined') ? js_debug_mode : 1;
	//set globally or toggle [1|0]

	NXAJAX.preRun = function () {

		//set some global objects
		NXAJAX.data = {};
		NXAJAX.post = {};
		NXAJAX.payload = {};

		//some preset data -  autoloading (if applicable)
		NXAJAX.payload['more_results'] = 0;
		NXAJAX.payload['next_url'] = '';

	}();


	/**------------------------------------------------------------------------
	 * some actions on start
	 *------------------------------------------------------------------------*/

	NXAJAX.onStart = function () {

		//hide some elements
		if (typeof NXAJAX.OBJ.attr("data-onstart-hide") != 'undefined' && NXAJAX.OBJ.attr("data-onstart-hide") != '') {
			var dom_element = NXAJAX.OBJ.attr("data-onstart-hide");
			$(dom_element).hide();
		}

		//show some elements
		if (typeof NXAJAX.OBJ.attr("data-onstart-show") != 'undefined' && NXAJAX.OBJ.attr("data-onstart-show") != '') {
			var dom_element = NXAJAX.OBJ.attr("data-onstart-show");
			$(dom_element).show();
		}

	}();


	/**------------------------------------------------------------------------
	 * output debug data - only if debug mode is enabled
	 * [returns] - bool
	 *------------------------------------------------------------------------*/

	NXAJAX.log = function (payload1, payload2) {
		if (NX.debug_javascript) {
			if (payload1 != undefined) {
				console.log(payload1);
			}
			if (payload2 != undefined) {
				console.log(payload2);
			}
		}
	};

	/**------------------------------------------------------------------------
	 * get all the required data from the event
	 * [returns] - bool
	 *------------------------------------------------------------------------*/

	NXAJAX.eventData = function (obj) {

		//debug
		NXAJAX.log("[ajax] eventData() - setting all data attributes from event - [payload]:", obj, );

		///save this button/object etc
		NXAJAX.obj = obj;

		//require data
		NXAJAX.data.url = obj.attr("data-url");

		//ajax request method type
		if (typeof obj.attr("data-ajax-type") != 'undefined' && obj.attr("data-ajax-type") != '') {
			NXAJAX.data.ajax_type = obj.attr("data-ajax-type");
		} else {
			NXAJAX.data.ajax_type = 'GET';
		}

		//optional data (loading animation target)
		if (typeof obj.attr("data-loading-target") != 'undefined' && obj.attr("data-loading-target") != '') {
			NXAJAX.data.loading_target = obj.attr("data-loading-target");
		} else {
			NXAJAX.data.loading_target = 'foo';
		}

		//optional data (button loading animation target)
		if (typeof obj.attr("data-button-loading-annimation") != 'undefined' && obj.attr("data-button-loading-annimation") == 'yes') {
			NXAJAX.data.button_loading_animation = 'yes';
		} else {
			NXAJAX.data.button_loading_animation = 'no';
		}


		//optional data (disable button on click)
		if (typeof obj.attr("data-button-disable-on-click") != 'undefined' && obj.attr("data-button-disable-on-click") == 'yes') {
			NXAJAX.data.button_disable_on_click = 'yes';
		} else {
			NXAJAX.data.button_disable_on_click = 'no';
		}

		//optional data (loading annimation class)
		if (typeof obj.attr("data-loading-class") != 'undefined' && obj.attr("data-loading-class") != '') {
			NXAJAX.data.loading_class = obj.attr("data-loading-class");
		} else {
			NXAJAX.data.loading_class = 'loading';
		}

		//optional data (loading annimation overlay target)
		if (typeof obj.attr("data-loading-overlay-target") != 'undefined' && obj.attr("data-loading-overlay-target") != '') {
			NXAJAX.data.overlay_target = obj.attr("data-loading-overlay-target");
		} else {
			NXAJAX.data.overlay_target = 'foo';
		}

		//optional data (loading annimation overlay class)
		if (typeof obj.attr("data-loading-overlay-classname") != 'undefined' && obj.attr("data-loading-overlay-classname") != '') {
			NXAJAX.data.overlay_classname = obj.attr("data-loading-overlay-classname");
		} else {
			NXAJAX.data.overlay_classname = '';
		}

		//optional data (show or hide progress bar on top of page)
		if (typeof obj.attr("data-progress-bar") != 'undefined' && obj.attr("data-progress-bar") == 'hidden') {
			NXAJAX.data.progress_bar = 'hidden';
		} else {
			NXAJAX.data.progress_bar = 'show';
		}

		//optional data (enable or display popup notifciations)
		if (typeof obj.attr("data-notifications") != 'undefined' && obj.attr("data-notifications") == 'disabled') {
			NXAJAX.data.show_notification = false;
		} else {
			NXAJAX.data.show_notification = true;
		}

		//[optional] - infinite scroll if applicable - the div for tracking page
		NXAJAX.data.infinite_scroll_marker = obj.attr("data-infinite-scroll-marker");


		//call back function 
		if (typeof obj.attr("data-postrun-functions") != 'undefined') {
			NXAJAX.data.postrun_functions = obj.attr("data-postrun-functions").split(",");
		} else {
			NXAJAX.data.postrun_functions = [];
		}

		//do not reset checkboxes when completing the response
		NXAJAX.data.skip_checkboxes_reset = obj.attr("data-skip-checkboxes-reset");

		//debug
		NXAJAX.log("[ajax] eventData() - current NXAJAX.data array content - [payload]:", NXAJAX.data);


		//reset loading target
		if (obj.attr("data-reset-loading-target")) {
			var target = obj.attr("data-loading-target");
			if (target != '' && target != null) {
				$("#" + target).html("");
			}
		}

		return;

	};



	/**------------------------------------------------------------------------
	 * validates all the required data for a valid ajax request
	 * [returns] - bool
	 *------------------------------------------------------------------------*/

	NXAJAX.validateRequired = function () {

		//debug
		NXAJAX.log("[ajax] validateRequired() - validating required data - [payload]:", NXAJAX);

		//inital
		var state = true;

		//required items
		var required = ['url'];
		//can add more to this array

		//loop through and validate all required
		$.each(required, function (index, value) {
			if (NXAJAX.data[value] == undefined) {
				state = false;
				//debug
				NXAJAX.log('[ajax] NXAJAX.validateRequired() - [error] required [NXAJAX.data] item is missing: (' + value + ') - [suggest] check data attributes');
			}
		});
		return state;
	};

	/**------------------------------------------------------------------------
	 * If event is a form submission (i.e.e search for) get all form field data
	 * and save to an object
	 * [returns] - object
	 *------------------------------------------------------------------------*/

	NXAJAX.processPostData = function (obj) {

		//debug
		NXAJAX.log('[ajax] processPostData() - adding form post data, if available - [payload]:', obj);

		//reset post object
		NXAJAX.post = {};

		if (obj.attr('type') == 'submit' || obj.attr('data-type') == 'search' || obj.attr('data-type') == 'form' || obj.attr('data-type') == 'PUT' || obj.attr('data-ajax-type') == 'post') {


			//get the form - a specified form ID (NB: it does not have to be an actual form, but can be a div, with form elements)
			if (typeof obj.attr('data-form-id') != 'undefined' && obj.attr('data-form-id') != '') {
				var form = $("#" + obj.attr('data-form-id'));
			} else {
				//assume the parent form of this button
				var form = obj.parents('form:first');
			}

			//find all [input, textarea, select]
			form.find("input, textarea, select").each(function () {
				var field_name = $(this).attr('name');

				//special consideration for ckeditor textarea (must have data attr [data-type = ckeditor])
				if ($(this).attr('data-type') == 'ckeditor') {
					var field_id = $(this).attr('id');
					var field_value = CKEDITOR.instances[field_id].getData();
				} else {
					//general form fields
					var field_value = $(this).val();

					//check boxes form fields (seems obvoius but without this does not worl as expected)
					if ($(this).is(':checkbox')) {
						if ($(this).prop('checked')) {
							var field_value = 'on';
						} else {
							var field_value = '';
						}

					}
				}
				//add to post object
				if (field_name != undefined) {
					NXAJAX.post[field_name] = field_value;
				}
			});
		}
		//debug
		NXAJAX.log(NXAJAX.post);

		return;
	};

	/**------------------------------------------------------------------------
	 * [RENDER AJAX VIEW]
	 * - places all the payload into the right places
	 *------------------------------------------------------------------------*/

	NXAJAX.loadingAnimation = function (action) {

		//debug
		NXAJAX.log('[ajax] loadingAnimation() - setting to (' + action + ')');

		/** ---------------------------------------------------------------------------
		 * [OVERLAYS]
		 * - An ovelay class will be added or removed from the target dom element
		 * - Selector must be a DOM element ID
		 * - Data can be set via dom attributes (e.g for click events)
		 *             - data-loading-overlay-target='foo'
		 *             - data-loading-overlay-class='bar'
		 * - Data can also be set manually as a var in the calling HTML page
		 *             - NXAJAX.data.overlay_target = 'foo';
		 *             - NXAJAX.data.overlay_classname = 'bar';
		 *-----------------------------------------------------------------------------*/
		var overlay_target = NXAJAX.data.overlay_target;
		var overlay_class = NXAJAX.data.overlay_classname;

		//show or hide overlays
		if (action != undefined) {
			if (overlay_target != undefined && overlay_class != undefined) {
				//show
				if (action == 'show') {
					$("#" + overlay_target).addClass(overlay_class);
				}
				//hide
				if (action == 'hide') {
					$("#" + overlay_target).removeClass(overlay_class);
				}
			}
		}

		/** ---------------------------------------------------------------------------
		 * [LOADING ANNIMATIONS]
		 * - A loading class will be added or removed from the target dom element
		 * - Selector must be a DOM element ID
		 * - Data can be set via dom attributes (e.g for click events)
		 *             - data-loading-loading-target='foo'
		 *             - data-loading-loading-class='bar'
		 * - Data can also be set manually as a var in the calling HTML page
		 *             - NXAJAX.data.loading_target = 'foo';
		 *             - NXAJAX.data.loading_class = 'bar';
		 *-----------------------------------------------------------------------------*/
		var loading_target = NXAJAX.data.loading_target;
		var loading_class = NXAJAX.data.loading_class;

		//show or hide loading annimations
		if (action != undefined) {
			if (loading_target != undefined && loading_class != undefined) {
				//show
				if (action == 'show') {
					$("#" + loading_target).addClass(loading_class);
				}
				//hide
				if (action == 'hide') {
					$("#" + loading_target).removeClass(loading_class);
					//also remove
					$("#" + loading_target).removeClass('loading-placeholder');

				}
			}
		}

		return;
	};


	/**------------------------------------------------------------------------
	 * annimate clicked button
	 *------------------------------------------------------------------------*/
	NXAJAX.annimateButton = function () {
		if (NXAJAX.data.button_loading_animation == 'yes') {
			NXAJAX.obj.addClass('button-loading-annimation');
		}
	}

	/**------------------------------------------------------------------------
	 * disable clicked button
	 *------------------------------------------------------------------------*/
	NXAJAX.disableButton = function () {
		if (NXAJAX.data.button_disable_on_click == 'yes') {
			NXAJAX.obj.prop("disabled", true);
		}
	}

	/**------------------------------------------------------------------------
	 * remove button anniation
	 *------------------------------------------------------------------------*/
	NXAJAX.resetAnnimateButton = function () {
		NXAJAX.obj.removeClass('button-loading-annimation');
		NXAJAX.obj.prop("disabled", false);
	}


	/**------------------------------------------------------------------------
	 * enable any previously disabled button
	 *------------------------------------------------------------------------*/
	NXAJAX.resetDisableButton = function () {
		if (NXAJAX.data.button_disable_on_click == 'yes') {
			NXAJAX.obj.prop("disabled", false);
		}
	}

	/**------------------------------------------------------------------------
	 * get the ajax response and save to the main object
	 *------------------------------------------------------------------------*/

	NXAJAX.getPayload = function (obj) {

		NXAJAX.log('[ajax] getPayload() - processing response data from backend - [payload]:', obj);

		//state
		var state = true;

		//redirect request
		NXAJAX.payload.redirect_url = obj.redirect_url;
		NXAJAX.payload.delayed_redirect_url = obj.delayed_redirect_url;

		//reset tinymce editors
		NXAJAX.payload.tinymce_reset = obj.tinymce_reset;

		//reset tinymce editors
		NXAJAX.payload.tinymce_new_data = obj.tinymce_new_data;

		//dom html()
		NXAJAX.payload.dom_html = obj.dom_html;


		//dom html that is done at the end of all other actions
		NXAJAX.payload.dom_html_end = obj.dom_html_end;

		//dom val()
		NXAJAX.payload.dom_val = obj.dom_val;

		//dom dom_move_element
		NXAJAX.payload.dom_move_element = obj.dom_move_element;


		//dom state
		NXAJAX.payload.dom_state = obj.dom_state;

		//dom attributes
		NXAJAX.payload.dom_attributes = obj.dom_attributes;

		//dom propery
		NXAJAX.payload.dom_property = obj.dom_property;

		//dom css
		NXAJAX.payload.dom_css = obj.dom_css;

		//dom classes
		NXAJAX.payload.dom_classes = obj.dom_classes;

		//dom visibility
		NXAJAX.payload.dom_visibility = obj.dom_visibility;

		//dom chained effects
		NXAJAX.payload.dom_chained_effects = obj.dom_chained_effects;

		//our next offset
		NXAJAX.payload.offset = obj.offset;

		//do we any more results to follow (used by autoload)
		NXAJAX.payload.more_results = obj.more_results;

		//if this is for autoloading, the next autoload url
		NXAJAX.payload.next_url = obj.next_url;

		//get any notification
		NXAJAX.payload.notification = obj.notification;

		//browser url update
		NXAJAX.payload.dom_browser_url = obj.dom_browser_url;

		//postrun function
		NXAJAX.payload.postrun_functions = obj.postrun_functions;

		//postrun function
		NXAJAX.payload.dom_action = obj.dom_action;

		return state;

	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM HTML] (if applicable)
	 *
	 * [JQUERY]
	 *    $("#foo").html(bar); //replace
	 *    $("#foo").append(bar); //append
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_html'][0]['selector'] = '#main-table'       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_html'][0]['action'] = 'replace'             : replace|append
	 *    ['dom_html'][0]['value'] = 'html code here'       : new value
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/

	NXAJAX.updateDomHTML = function ($timing = '') {

		//get the payload
		if ($timing == 'end') {
			var payload = NXAJAX.payload.dom_html_end;
		} else {
			var payload = NXAJAX.payload.dom_html;
		}

		//debug
		NXAJAX.log('[ajax] updateDomHTML() - updating dom if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.action != undefined && value.value != undefined) {
						//replace
						if (value.action == 'replace') {
							$(value.selector).html(value.value);
						}
						//replace-wth
						if (value.action == 'replace-with') {
							$(value.selector).replaceWith(value.value);
						}
						//append
						if (value.action == 'append') {
							$(value.selector).append(value.value);
						}
						//prepend
						if (value.action == 'prepend') {
							$(value.selector).prepend(value.value);
						}
						//reset to empty
						if (value.action == 'reset') {
							$(value.selector).html('');
						}
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM ATTRIBUTES] (if applicable)
	 *
	 * [JQUERY]
	 *    $("#foo").attr('data-age', '24');
	 *    $("#foo").attr('src', 'image.jpg');
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_attributes'][0]['selector'] = '#main-table'       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_attributes'][0]['attr'] = 'src'                   : valid dom attribute
	 *    ['dom_attributes'][0]['value'] = 'image.jpg'            : new value
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomAttributes = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_attributes;

		//debug
		NXAJAX.log('[ajax] updateDomAttributes() - updating dom if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.attr != undefined && value.value != undefined) {
						//replace
						$(value.selector).attr(value.attr, value.value);
					}
				}
			});
		}
		return;
	};


	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [POST RUN FUNCTIONS]
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['postrun_function'][0]['value'] = 'nxFooBar'            : new value
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.postRunFunctions = function () {

		//get the payload
		var payload = NXAJAX.payload.postrun_functions;

		//debug
		NXAJAX.log('[ajax] postRunFunctions() - running an specified postrun js', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.value != undefined) {
						//i function exists, run it
						if (typeof window[value.value] === "function") {
							window[value.value]();
						}
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM ATTRIBUTES] (if applicable)
	 *
	 * [JQUERY]
	 *    $("#foo").prop('checked', true);
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_property'][0]['selector'] = '#agree-terms'       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_property'][0]['prop'] = 'checked'               : valid dom attribute
	 *    ['dom_property'][0]['value'] = 'true'                : new value
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomProperty = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_property;

		//debug
		NXAJAX.log('[ajax] updateDomProperty() - updating dom if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.prop != undefined && value.value != undefined) {
						//replace
						$(value.selector).prop(value.prop, value.value);
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM CSS] (if applicable)
	 *
	 * [JQUERY]
	 *    $("#foo").css('font-size', '24px');
	 *    $("#foo").attr('src', 'image.jpg');
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_attributes'][0]['selector'] = '#main-table'       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_attributes'][0]['attr'] = 'font-size'                   : valid css attribute
	 *    ['dom_attributes'][0]['value'] = '12px'            : new value
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomCSS = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_css;

		//debug
		NXAJAX.log('[ajax] updateDomCSS() - updating dom css if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.attr != undefined && value.value != undefined) {
						//replace
						$(value.selector).css(value.attr, value.value);
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [REDIRECT TO SPECIFIED URL]
	 *
	 * [JAVASCRIPT]
	 *    $("#foo").css('font-size', '24px');
	 *    $("#foo").attr('src', 'image.jpg');
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['redirect_url'] = 'http://www.google.com'

	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateRedirect = function () {

		//get the payload
		var payload = NXAJAX.payload.redirect_url;

		//debug
		NXAJAX.log('[ajax] updateRedirect() - url redirect request- [url]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload != '') {

			//close any open modals
			$('.modal').modal('hide');

			//redirect
			window.location.replace(payload);

			//progress bar start
			if (NXAJAX.data.progress_bar == 'show') {
				NProgress.set(0.99);
			}
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [REDIRECT TO SPECIFIED URL] after other actions have been done
	 *
	 * [JAVASCRIPT]
	 *    $("#foo").css('font-size', '24px');
	 *    $("#foo").attr('src', 'image.jpg');
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['redirect_url'] = 'http://www.google.com'

	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDelayedRedirect = function () {

		//get the payload
		var payload = NXAJAX.payload.delayed_redirect_url;

		//debug
		NXAJAX.log('[ajax] updateRedirect() - url redirect request- [url]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload != '') {

			//close any open modals
			$('.modal').modal('hide');

			//redirect
			window.location.replace(payload);

			//progress bar start
			if (NXAJAX.data.progress_bar == 'show') {
				NProgress.set(0.99);
			}
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM CLASSES] (if applicable)
	 *
	 * [JQUERY]
	 *    $("#foo").addClass('bar');
	 *    $("#foo").removeClass('bar');
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_classes'][0]['selector'] = '#main-table'       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_classes'][0]['action'] = 'add'                 : add|remove
	 *    ['dom_classes'][0]['value'] = 'some-class-name'      : new value
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomClasses = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_classes;

		//debug
		NXAJAX.log('[ajax] updateDomClasses() - updating dom if applicable - [payload]:', payload);
		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.action != undefined && value.value != undefined) {
						//add
						if (value.action == 'add') {
							$(value.selector).addClass(value.value);
						}
						//remove
						if (value.action == 'remove') {
							$(value.selector).removeClass(value.value);
						}
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM FADEIN&OUT] (if applicable)
	 *
	 * This is for a smoother chained effects like fadeout one element and fadein another.
	 * You can add more effects here
	 *
	 * [JQUERY]
	 *          //example fadeout & fadein
	 *    		$("#foo").fadeOut(function() {
	 *             $("#foo").fadeIn();
	 *         });
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_chained_effects'][0]['selector_first'] = '#foo'       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_chained_effects'][0]['selector_second'] = '#bar'      : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_chained_effects'][0]['effect'] = 'fadeout-fadein'     : valid css attribute
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomChainedEffects = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_chained_effects;

		//debug
		NXAJAX.log('[ajax] updateDomChainedEffects() - updating dom chained effects if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector_first != undefined && value.selector_second != undefined && value.effect != undefined) {

						//[effect] fadeout and fadein
						if (value.effect == 'fadeout-fadein') {
							$(value.selector_first).fadeOut(function () {
								$(value.selector_second).fadeIn();
							});
						}

						//[effect] fadeout and fadein
						if (value.effect == 'fadein-fadeout') {
							$(value.selector_first).fadeIn(function () {
								$(value.selector_second).fadeOut();
							});
						}

						//[effect] slideup slidedown
						//TO-DO																		
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM VISIBILITY] (if applicable)
	 *
	 * [JQUERY]
	 *    $("#foo").('show');
	 *    $("#foo").('hide');
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_visibility'][0]['selector'] = '#main-table'       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_visibility'][0]['action'] = 'show'                 : show|hide|slideup|slideup-slow|fadeout|fadeout-slow|fadein|fadein-slow
	 *                                                               close-modal | enable | disable
	 * 
	 * [REMOVING DOM ELEMENT]
	 *   ['dom_visibility'][0]['action'] = 'show'                 : hide-remove|slideup-remove|slideup-slow-remove|fadeout-remove|fadeout-slow-remove
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomVisibility = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_visibility;

		//debug
		NXAJAX.log('[ajax] updateDomVisibility() - updating dom if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.action != undefined) {
						//show
						if (value.action == 'show') {
							$(value.selector).show();
						}
						//remove
						if (value.action == 'hide') {
							$(value.selector).hide();
						}
						//show
						if (value.action == 'show-flex') {
							$(value.selector).css('display', 'flex');
						}
						//remove-remove
						if (value.action == 'hide-remove') {
							$(value.selector).hide();
							$(value.selector).remove();
						}
						//slide up
						if (value.action == 'slideup') {
							$(value.selector).slideUp();
						}
						//slide up & remove
						if (value.action == 'slideup-remove') {
							$(value.selector).slideUp();
							$(value.selector).remove();
						}
						//slide up slow
						if (value.action == 'slideup-slow') {
							$(value.selector).slideUp("slow");
						}
						//slide up slow & remove
						if (value.action == 'slideup-slow-remove') {
							$(value.selector).slideUp("slow");
							$(value.selector).remove();
						}
						//slide down
						if (value.action == 'slidedown') {
							$(value.selector).slideDown();
						}
						//slide down slow
						if (value.action == 'slidedown-slow') {
							$(value.selector).slideDown("slow");
						}
						//fadeout
						if (value.action == 'fadeout') {
							$(value.selector).fadeOut();
						}
						//fadeout & remove
						if (value.action == 'fadeout-remove') {
							$(value.selector).fadeOut();
							$(value.selector).remove();
						}
						//fadeout-slow
						if (value.action == 'fadeout-slow') {
							$(value.selector).fadeOut("slow");
						}
						//fadeout-slow-remove
						if (value.action == 'fadeout-slow-remove') {
							$(value.selector).fadeOut("slow");
							$(value.selector).remove();
						}
						//fadein
						if (value.action == 'fadein') {
							$(value.selector).fadeIn();
						}
						//fadein-slow
						if (value.action == 'fadein-slow') {
							$(value.selector).fadeIn("slow");
						}
						//close modal window
						if (value.action == 'close-modal') {
							$(value.selector).modal('hide');
						}
						//disable
						if (value.action == 'disable') {
							$(value.selector).prop("disabled", true);
						}
						//disable
						if (value.action == 'disable') {
							$(value.selector).prop("disabled", true);
						}
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM STATE] (if applicable)
	 * enable or disable buttons, fields and links
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_visibility'][0]['selector'] = '#submit-button '       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_visibility'][0]['action'] = 'enabled'                 : enabled|disabled
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomState = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_state;

		//debug
		NXAJAX.log('[ajax] js_toggle_editor_button() - updating dom if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.action != undefined) {

						//enable elements
						if (value.action == 'enabled') {
							//buttons
							if ($(value.selector).is('button') || $(value.selector).is('input')) {
								$(value.selector).prop("disabled", false);
							}
							//links
							if ($(value.selector).is('a')) {
								$(value.selector).attr('disabled', false);
							}
						}

						//disable elements
						if (value.action == 'disabled') {
							//buttons
							if ($(value.selector).is('button') || $(value.selector).is('input')) {
								$(value.selector).prop("disabled", true);
							}
							//links
							if ($(value.selector).is('a')) {
								$(value.selector).attr('disabled', true);
							}
						}
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - DOM VAL]
	 * update the value or form fields. Value can also be sent as blank, for resets
	 *
	 * [JQUERY]
	 *   $(foo).val(bar)
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_val'][0]['selector'] = '#submit-button '       : valid dom selector '.some_class' | '#some_id' | '[input-type=""]'
	 *    ['dom_val'][0]['value'] = 'bar'                 : string|null
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/

	NXAJAX.updateDomVAL = function (obj) {

		//get the payload
		var payload = NXAJAX.payload.dom_val;

		//debug
		NXAJAX.log('[ajax] updateDomVAL() - resetting form field values - [payload]:', obj);

		//update the DOM (id|class)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.value != undefined) {
						//check boxes
						if ($(value.selector).is('checkbox') || $(value.selector).is('radio')) {
							if (value.value == 'checked') {
								$(value.selector).removeAttr('checked');
							} else {
								$(value.selector).addAttr('checked');
							}
						} else {
							//all other input fields
							if ($(value.selector).is('textarea')) {
								//basic reset
								$(value.selector).val(value.value);
								//incase its a ckeditor textarea
								try {
									var element_name = value.selector;
									element_name = element_name.replace('.', '');
									element_name = element_name.replace('#', '');
									CKEDITOR.instances[element_name].updateElement();
									CKEDITOR.instances[element_name].setData(value.value);
								} catch (err) {
									//do nothing
								}
							} else {
								$(value.selector).val(value.value);
							}
						}
					}
				}
			});
		}
		return;
	};


	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [MOVE DOM ELEMENTS]
	 *  move a dom element from one location, to inside a new a parent element
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_move_element'][0]['element'] = '#somediv'       : valid dom selector '.some_class' | '#some_id''
	 *    ['dom_move_element'][0]['newparent'] = 'bar'          : valid dom selector '.some_class' | '#some_id''
	 *    ['dom_move_element'][0]['method'] = 'bar'             : prepend-to|append-to|replace|replace-with
	 *    ['dom_move_element'][0]['visibility'] = 'bar'         : show|hide|null
	 *------------------------------------------------------------------------------------------------------------------------------------*/

	NXAJAX.updateMoveElement = function (obj) {

		//get the payload
		var payload = NXAJAX.payload.dom_move_element;

		//debug
		NXAJAX.log('[ajax] updateMoveElement() - moving dom elements to a new location - [payload]:', obj);

		//update the DOM (id|class)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.element != undefined && value.newparent != undefined && value.method != undefined) {

						//prepend to
						if (value.method == 'prepend-to') {
							$(value.element).prependTo(value.newparent);
						}

						//append to
						if (value.method == 'append-to') {
							$(value.element).prependTo(value.newparent);
						}

						//replace
						if (value.method == 'replace') {
							$(value.newparent).html('');
							$(value.element).prependTo(value.newparent);
						}

						//replace
						if (value.method == 'replace-with') {
							$(value.newparent).replaceWith($(value.element));
						}

						//end visibility
						if (value.visibility != undefined) {
							if (value.visibility == 'show') {
								$(value.element).show();
							}
							if (value.visibility == 'hide') {
								$(value.element).hide();
							}
						}
					}
				}
			});
		}
		return;
	};


	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [RESET TINYMCE EDITORS]
	 * update the value or form fields. Value can also be sent as blank, for resets
	 *
	 * [JQUERY]
	 *   $(foo).val(bar)
	 *
	 * [EXAMPLE DATA SENT]
	 * 	NXAJAX.payload.tinymce_reset = obj.tinymce_reset;
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/

	NXAJAX.tinyMCEReset = function () {

		//get the payload
		var payload = NXAJAX.payload.tinymce_reset;

		//debug
		NXAJAX.log('[ajax] resetTinyMCE() - resetting tinymce editors - [payload]:', payload);

		//update the DOM (id|class)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined) {
						//reset editor (only is it exixts)
						if ($("#" + value.selector).length) {
							tinymce.get(value.selector).setContent('');
						}
					}
				}
			});
		}
		return;
	};


	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [RESET TINYMCE EDITORS]
	 * set new data to tinymce
	 *------------------------------------------------------------------------------------------------------------------------------------*/

	NXAJAX.tinyMCENewData = function () {

		//get the payload
		var payload = NXAJAX.payload.tinymce_new_data;

		//debug
		NXAJAX.log('[ajax] tinyMCENewData() -setting new data for tinymce editors - [payload]:', payload);

		//update the DOM (id|class)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.value != undefined) {
						//reset editor (only is it exixts)
						if ($("#" + value.selector).length) {
							tinymce.get(value.selector).setContent(value.value);
						}
					}
				}
			});
		}
		return;
	};

	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - NOTIFICATION] (if applicable)
	 *
	 * [JQUERY]
	 *
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['notification']['type'] = 'error'              : error|success
	 *    ['notification']['value'] = 'request error'       : message
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/

	NXAJAX.notification = function () {

		//get the payload
		var payload = NXAJAX.payload.notification;

		//are we allowed to show notifications with this request
		if (!NXAJAX.data.show_notification) {
			if (payload != undefined && (payload.type != undefined && (payload.type == 'force-error' || payload.type == 'force-success'))) {
				//do nothing. this is a forced error. 
			} else {
				//exit - do not show notifications
				return;
			}
		}

		//to send to notification method
		var obj = {};

		//debug
		NXAJAX.log('[ajax] notification() - displaying notification if applicable - [payload]:', payload);

		//validation
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			if (payload.type != undefined && payload.value != undefined) {
				//error & waring (same thing)
				if (payload.type == 'error' || payload.type == 'warning' || payload.type == 'force-error') {
					obj['type'] = 'error';
					obj['message'] = payload.value;
					NX.notification(obj);
				} else {
					//all others
					obj['type'] = payload.type;
					obj['message'] = payload.value;
					NX.notification(obj);
				}
			}
		}
		return;
	};


	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [UPDATE - BROWSER URL]
	 *
	 *
	 * [EXAMPLE DATA SENT]
	 *    ['dom_browser_url']['title']
	 *    ['dom_browser_url']['url'] 
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomBrowserUrl = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_browser_url;

		//debug
		NXAJAX.log('[ajax] updateDomBrowserUrl() - updating browser url if applicable - [payload]:', payload);

		//update the DOM (id|class|literal)
		if (payload != undefined && typeof payload == 'object') {

			if (payload.title != undefined && payload.url != undefined) {
				//update browser url and history
				history.pushState({}, payload.title, payload.url);
			}
		}
		return;
	};



	/**-----------------------------------------------------------------------------------------------------------------------------------
	 * [DOM ACTIONS]
	 *
	 * Various other dom actions, such as trigger click etc
	 *
	 *------------------------------------------------------------------------------------------------------------------------------------*/
	NXAJAX.updateDomAction = function () {

		//get the payload
		var payload = NXAJAX.payload.dom_action;

		//debug
		NXAJAX.log('[ajax] updateDomAction() - doing an action on a dom element - [payload]:', payload);

		//update the DOM (id|class)
		if (payload != undefined && typeof payload == 'object') {
			//loop through the payload and update the dom
			$.each(payload, function (index, value) {
				//sanity check - make sure its an object
				if (typeof value == 'object') {
					if (value.selector != undefined && value.action != undefined && value.value != undefined) {

						//trigger [e.g. $(element).trigger('click')];
						if (value.action == 'trigger') {
							$(value.selector).trigger(value.value);
						}

						//trigger select change
						if (value.action == 'trigger-select-change') {
							$(value.selector).val(value.value).trigger('change');
						}

					}
				}
			});
		}
		return;
	};


	/**--------------------------------------------------------------CUSTOM FUNCTIONS HERE ---------------------------------------------- */

	/**-----------------------------------------------------------------------
	 * [REINITIALIZE JS FUNCTIONALITY FOR AJAX LOADED DOM]
	 * Some javascript functions are not available for content added to the dom
	 * using ajax. The solution is to just reinitialize the function for the
	 * whole page.
	 *
	 * [CHECKING IF FUNCTION EXISTS]
	 * Its good to check if function exists to avoid undefined errors
	 *     - jquery /jquery plugin functions
	 *              if ($.fn.someFunction) {
	 *
	 * 	   - plain js functions
	 *              if ( typeof someFunction === "function") {
	 *
	 *------------------------------------------------------------------------*/
	NXAJAX.reinitialiseDom = function () {

		//debug
		NXAJAX.log("[ajax] reinitialise_dom() - re-initialising some javascript functions to account for new dom content");

		/** -------------------------------------------
		 * bootstrap
		 * ------------------------------------------*/
		NXbootstrap();

		/** -------------------------------------------
		 * tooltipe (incase we changed text)
		 * ------------------------------------------*/
		$('[data-toggle="tooltip"]').tooltip('hide');
	};


	//get event data
	NXAJAX.eventData(obj);

	//get post data
	NXAJAX.processPostData(obj);

	var nxTime0 = performance.now();

	//state
	var state = true;

	//-----kill any similar ajax request thats already running (optional)----
	if (typeof ajax_request !== undefined && ajax_request && ajax_request.readyState !== 4) {
		ajax_request.abort();
	}

	//validate input data
	if (!NXAJAX.validateRequired()) {
		return;
	}

	/**------------------------------------------------------------------------
	 * [AJAX REQUEST]
	 * send request to the backend and get the results
	 *------------------------------------------------------------------------*/
	NXAJAX.log('[ajax] ajaxRequest() - starting ajax request - [url]: ' + NXAJAX.data.url + ' [post payload]:', NXAJAX.post);


	//Laravel DELETE & PUT Fix
	if (NXAJAX.data.ajax_type == 'PUT') {
		NXAJAX.data.ajax_type = 'POST';
		NXAJAX.post['_method'] = 'PUT';
	}
	if (NXAJAX.data.ajax_type == 'DELETE') {
		NXAJAX.data.ajax_type = 'POST';
		NXAJAX.post['_method'] = 'DELETE';
	}

	//start ajax request
	var ajax_request = $.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		type: NXAJAX.data.ajax_type,
		url: NXAJAX.data.url,
		dataType: 'json',
		data: NXAJAX.post,

		/** --------------------------------------------------------------------*
		 * About to start ajax request
		 * Show any ajax loading annimation etc
		 *----------------------------------------------------------------------*/
		beforeSend: function (xhr) {
			//progress bar start
			if (NXAJAX.data.progress_bar == 'show') {
				NProgress.set(0.7);
			}
			//loading or overlay annimation
			NXAJAX.loadingAnimation('show');

			//annimate clicked button
			NXAJAX.annimateButton();

			//disable clicked button
			NXAJAX.disableButton();

			//disable submit button if applicable
			if (obj.attr('data-on-start-submit-button') == 'disable') {
				obj.prop('disabled', true);
			}
		},

		/**------------------------------------------------------------------------
		 * request was successful (header status:200)
		 *  - This is a list of all the various items we can now do.
		 *  - Validation and applicability will be done directly by each function
		 *------------------------------------------------------------------------*/
		success: function (data) {

			//start timer to monitor perfomenace
			var t1 = performance.now();

			NXAJAX.log('[ajax] ajaxRequest() - success - we have a response from the server ' + data);

			//process the payload
			NXAJAX.getPayload(data);

			//was there a redirect request
			NXAJAX.updateRedirect();

			///sto here if we are doing a redirect
			if (typeof NXAJAX.payload.redirect_url != 'undefined' && NXAJAX.payload.redirect_url != '') {
				return;
			}


			//dom attributes - before view render
			NXAJAX.updateDomAttributes();

			//dom css - before view render
			NXAJAX.updateDomCSS();

			//update class e.g. $(foo).addClass(bar)
			NXAJAX.updateDomClasses();

			//dom chained effects
			NXAJAX.updateDomChainedEffects();

			//update HTML
			NXAJAX.updateDomHTML();

			//update VAL
			NXAJAX.updateDomVAL();

			//update PROP
			NXAJAX.updateDomProperty()

			//move dom elements
			NXAJAX.updateMoveElement();

			//update visibility
			NXAJAX.updateDomVisibility();


			//update HTML
			NXAJAX.updateDomHTML('end');


			//update STATE e.g. $(foo)..prop("disabled", true);
			NXAJAX.updateDomState();

			//dom action = e,g trigger click
			NXAJAX.updateDomAction();

			//show any notifications
			NXAJAX.notification();

			//was there a delayed redirect request
			NXAJAX.updateDelayedRedirect();

			//reinitialise js function for new dom content (cam skip this if it causing a ux issue) : $jsondata['skip_dom_reset'] = true;
			if (typeof data.skip_dom_reset == 'undefined') {
				NXAJAX.reinitialiseDom();
			}

			//reinitialise js function for new dom content (cam skip this if it causing a ux issue) : $jsondata['skip_dom_tinymce'] = true;
			if (typeof data.skip_dom_tinymce == 'undefined') {
				nxTinyMCEBasic();
				nxTinyMCELite();
				nxTinyMCEExtended();
				nxTinyMCEExtendedLite();
			}

			/** progress bar and annimation - finished*/
			if (NXAJAX.data.progress_bar == 'show') {
				NProgress.done();
			}

			//enable submit button if applicable
			if (obj.attr('data-on-start-submit-button') == 'disable') {
				obj.prop('disabled', false);
			}

			//reset list pages - actions check boxes
			if (NXAJAX.data.skip_checkboxes_reset) {
				//do nothing
			} else {
				if (typeof NX.listCheckboxesReset !== 'undefined') {
					NX.listCheckboxesReset();
				}
			}

			//custom post run functions
			$.each(NXAJAX.data.postrun_functions, function (index, val) {
				//i function exists, run it
				if (typeof window[val] === "function") {
					window[val]();
				}
			});

			//post run functions
			NXAJAX.postRunFunctions();

			///rerun some functions (needed for items in modals, where the html replace happens after)
			if (typeof data.rerun != 'undefined' && data.rerun != '') {
				if (data.rerun == 'updateDomAttributes') {
					NXAJAX.log('[ajax] re-executing updateDomAttributes()');
					NXAJAX.updateDomAttributes();
				}
			}

			//reset tinymce editors
			NXAJAX.tinyMCEReset();

			//set new data for tinymce editor
			NXAJAX.tinyMCENewData();

			//update browser url if applicable
			NXAJAX.updateDomBrowserUrl();

			//loading or overlay annimation
			NXAJAX.loadingAnimation('hide');

			//remove button annimation
			NXAJAX.resetAnnimateButton();

			//enable disabled button
			NXAJAX.resetDisableButton();


			//perfomance end
			var tx = performance.now();
			NXAJAX.log('[ajax] Complete request completed in ' + (tx - nxTime0) + 'milliseconds');
			NXAJAX.log('[ajax] Frontend part of the request completed in ' + (tx - t1) + 'milliseconds');

		},

		/**------------------------------------------------------------------------
		 * request was NOT successful (header status:400)
		 *------------------------------------------------------------------------*/
		error: function (data, jqXHR) {

			NXAJAX.log('[ajax] ajaxRequest() - error - we have an error from the server - payload:');

			//has the laravel session timedout?
			var $session_timeout = false;

			//laravel 401 timeout status
			if (typeof data.status != 'undefined' && data.status == 401) {
				NXAJAX.log('[ajax] ajaxRequest() - server error - session timeout');
				$session_timeout = true;
			}

			//laravel - token mismatch response
			if (typeof data.responseJSON != 'undefined' && typeof data.responseJSON.message != 'undefined' && (data.responseJSON.message == 'CSRF token mismatch.' || data.responseJSON.message == 'Unauthenticated.')) {
				NXAJAX.log('[ajax] ajaxRequest() - server error - session timeout');
				$session_timeout = true;
			}


			//Laaravel session authetication error - show login modal
			if ($session_timeout) {

				//close all modals & show login modal
				if (NX.session_login_popup == 'enabled') {
					//session timeout login popup login modal
					//$('.modal').modal('hide');
					//$("#reloginModal").modal("show");
				}

				//end annimations
				NXAJAX.loadingAnimation('hide');
				NProgress.done();

				//finish
				return;
			}


			//show any other error message
			if (typeof data.responseJSON != 'undefined') {
				//payload
				NXAJAX.getPayload(data.responseJSON);
				//show any notifications
				NXAJAX.notification();
			}


			/** progress bar and annimation - finished*/
			if (NXAJAX.data.progress_bar == 'show') {
				NProgress.done();
			}

			//enable submit button if applicable
			if (obj.attr('data-on-start-submit-button') == 'disable') {
				obj.prop('disabled', false);
			}

			//loading or overlay annimation
			NXAJAX.loadingAnimation('hide');

			//remove button annimation
			NXAJAX.resetAnnimateButton();

			//enable disabled button
			NXAJAX.resetDisableButton();

		}
	});
};

$(document).ready(function () {

	/**-----------------------------------------------------------------------
	 *  [NEW AJAX RECORD REQUEST]
	 *  [event] -  clicked on button or link etc
	 *  [action] - validate required data
	 *           - send request to ajax
	 *------------------------------------------------------------------------*/
	$(document).on('click', '.js-ajax-ux-request, .ajax-request, .js-ajax-request', function (e) {
		e.preventDefault();
		//call the function to process request
		nxAjaxUxRequest($(this));
	});

	/**-----------------------------------------------------------------------
	 *  [NEW AJAX RECORD REQUEST]
	 *  same as above but allows default object action
	 *  good when used with elements like checkboxes
	 *------------------------------------------------------------------------*/
	$(document).on('click', '.js-ajax-ux-request-default', function (e) {
		//call the function to process request
		nxAjaxUxRequest($(this));
	});
});