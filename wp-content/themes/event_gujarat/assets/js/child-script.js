/**
 * Wrapper function to safely use $
 */
function gujChildWrapper($) {

    /*global generalPar:false, ajaxPar:false, gform:false, wp, GUJModals */
    var gujChild = {

        /**
         * Main entry point
         */
        init: function () {
            gujChild.registerEventHandlers();
            gujChild.commonLoad();
            gujChild.eventValidate();
            gujChild.autoAddress();
        },

        /**
         * Registers event handlers
         */
        registerEventHandlers: function () {
            $(document).on('click','#resetFilter',gujChild.resetForm);
            $(document).on('submit','#eventFrm',gujChild.eventAdd);
            $(document).on('submit','#addOrg',gujChild.orgAdd);
            $(document).on('click','#event_comment',gujChild.orgComm);
            // $(document).on('change','#active-inactive',gujChild.eventFilterStatus);
            // $(document).on('change','#all-event-organiser',gujChild.eventFilterOrg);
            // $(document).on('change','#all-event-constituencies',gujChild.eventFilterContin);
            $(document).on('click','#event_post_delete',gujChild.deleteEvent);
            $(document).on('submit', '#login_frm', gujChild.loginUser);
            $(document).on('submit', '#registration_frm', gujChild.registerUser);
            $(document).on('submit', '#dash_registration_frm', gujChild.registerDashUser);
            $(document).on('submit', '#update_user', gujChild.updateUser);
            $(document).on('click', '.toggle-password', gujChild.showPassword);
            $(document).on('click','#usr_delete',gujChild.deleteUser);
            $(document).on('click','#org_delete',gujChild.deleteOrganiser);
            $(document).on('click','#google_pin_drop',gujChild.pinDropLocation);
            $(document).on('click','#filters_event',gujChild.allEventFilters);
        },

         /**
         * Commom function load init handlers
         */
        commonLoad: function () {

            $(".select-boxMain select, .constituencies-label select").select2();

            var table = $('.data-tables').DataTable({ 
                select: false,
                "bSort": false,
                "columnDefs": [{
                    className: "Name", 
                    // "targets":[0],
                    "visible": false,
                    "searchable":false
                }]
            });//End of create main table 

            var selected_date = $(".datepicker").val();
            if (selected_date == '') {
                $('.datepicker1').prop('disabled', true);
            }
            $(".datepicker").datepicker({
                minDate: 0,
                changeMonth: true,
                changeYear: true,
                dateFormat:'dd-mm-yy',
                onSelect: function (date) {
                    selected_date = $(this).val();
                      if (selected_date != '') {
                        $('.datepicker1').prop('disabled', false);
                      }
                    var date2 = $('.datepicker').datepicker('getDate');
                    date2.setDate(date2.getDate());
                    $('.datepicker1').datepicker('setDate', date2);
                    //sets minDate to dt1 date + 1
                    $('.datepicker1').datepicker('option', 'minDate', date2);
                }
            });

            $('.datepicker1').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat:'dd-mm-yy',
            });

            $('.timepicker').timepicker({
                timeFormat: 'h:mm p',
                interval: 15,
                minTime: '00',
                maxTime: '12:00pm',
                // defaultTime: '07',
                //startTime: '00:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
            $('.timepicker1').timepicker({
                timeFormat: 'h:mm p',
                interval: 15,
                minTime: '00',
                maxTime: '12:00pm',
                // defaultTime: '08',
                //startTime: '08:00AM',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
        },

        // Add event filter callback
        // eventFilterStatus: function (e) {
        //     e.preventDefault();
        //     var table =  $('.data-tables').DataTable();
        //     table.columns(5).search("(^"+this.value+")",true,false ).draw();  
        // },
        // eventFilterOrg: function (e) {
        //     e.preventDefault();
        //     var table =  $('.data-tables').DataTable();
        //     console.log(this.value);
        //     table.columns(2).search("(^"+this.value+")",true,false ).draw();  
        // },
        // eventFilterContin: function (e) {
        //     e.preventDefault();
        //     var table =  $('.data-tables').DataTable();
        //     console.log(this.value);
        //     table.columns(1).search("(^"+this.value+")",true,false ).draw();  
        // },

        // Reset Map form
        resetForm: function (e) {
            e.preventDefault();
            $('#filter-store').trigger("reset");
            location.reload();
        }, 

        // Validate Add Event Form
        eventValidate: function (e) {

            $.validator.setDefaults({
              debug: true,
              success: "valid"
            });

            $("#eventFrm").validate({
                ignore: "",
                // debug: false,
                rules: {
                    eventname: {
                      required: true,
                      minlength : 2,
                      maxlength: 30,
                    },
                    'organiser[]': {
                        required: true,
                    },
                    'eventconstituencies[]': {
                        required: true,
                    },
                    eventlocation : {
                        required: true,
                        minlength : 2,
                        maxlength: 500,
                    },
                    startdate : {
                        required: true,
                    },
                    enddate : {
                        required: true,
                    }         
                },
                messages: {
                    eventname: "Please enter event name.",
                    'organiser[]': "Please select at least one organiser.",
                    'eventconstituencies[]': "Please select at least one constituencies.",
                    eventlocation: "Please enter event location.",
                    startdate: "Please select start date.",
                    enddate: "Please select end date.",
                },
                onkeyup: false
            });

            $("#addOrg").validate({
                ignore: "",
                rules: {
                    organisername : {
                        required: true,
                    }         
                },
                messages: {
                    organisername: "Please enter organiser name",
                },
                onkeyup: false
            });

            $("#addComm").validate({
                ignore: "",
                rules: {
                    commentname : {
                        required: true,
                    }         
                },
                messages: {
                    commentname: "Please enter organiser name",
                },
                onkeyup: false
            });

            $("#login_frm").validate({
                ignore: "",
                rules: {
                    email_address: {
                      required: true,
                      minlength : 2,
                      maxlength : 30,
                    },
                    password : {
                        required: true,
                        minlength : 8,
                        maxlength : 30,
                    }          
                },
                messages: {
                    email_address: {
                        required: "Please enter your username / email address",
                    },
                    password: "Please enter valid your password",
                },
                onkeyup: false
            });

            $("#registration_frm").validate({
                ignore: "",
                rules: {
                    username: "required",
                    first_name: "required",
                    last_name: "required",
                    email_address: {
                      required: true,
                      email: true,
                    },
                    password : {
                        required: true,
                        minlength : 8
                    },
                    cpassword : {
                        required: true,
                        minlength : 8,
                        equalTo : "#password"
                    }           
                },
                messages: {
                    username: "Please enter your username",
                    first_name: "Please enter your firstname",
                    last_name: "Please enter your lastname",
                    email_address: {
                        required: "Please enter a valid email address",
                        email: "Please enter a valid email address",
                    },
                    password : "Please enter valid password",
                    cpassword : "Please enter valid confirm password"
                },
                onkeyup: false
            });

            $("#dash_registration_frm").validate({
                ignore: "",
                rules: {
                    userrole: "required",
                    username: "required",
                    first_name: "required",
                    last_name: "required",
                    email_address: {
                      required: true,
                      email: true,
                    },
                    password : {
                        required: true,
                        minlength : 8
                    },
                    cpassword : {
                        required: true,
                        minlength : 8,
                        equalTo : "#password"
                    }           
                },
                messages: {
                    userrole: "Please select user role",
                    username: "Please enter your username",
                    first_name: "Please enter your firstname",
                    last_name: "Please enter your lastname",
                    email_address: {
                        required: "Please enter a valid email address",
                        email: "Please enter a valid email address",
                    },
                    password : "Please enter valid password",
                    cpassword : "Please enter valid confirm password"
                },
                onkeyup: false
            });

            $("#update_user").validate({                
                ignore: "",
                rules: {
                    first_name: "required",
                    last_name: "required",
                    upassword : {
                        // required: true,
                        minlength : 8
                    },
                    ucpassword : {
                        // required: true,
                        minlength : 8,
                        equalTo : "#upassword"
                    }           
                },
                messages: {
                    first_name: "Please enter your firstname",
                    last_name: "Please enter your lastname",
                    upassword : "Please enter valid password",
                    ucpassword : "Please enter valid confirm password"
                },
                onkeyup: false
            });
        },       

        // Add event form using frontend callback
        eventAdd: function (e) {
            e.preventDefault();
            if(!$("#eventFrm").valid())
            {return false;}

            $("#event_btn").prop('disabled', true);
            var passData = $("#eventFrm").serialize();
            passData = passData+'&action=add_event&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({
                    beforeSend: function(){
                        $('.ajax-loader').css("visibility", "visible");
                    },
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            if (response.type === 'update') {
                                $('#eventName').val(response.name);
                            } else {
                                $('#event-constituencies').val(' ').trigger("change");
                                $("#eventFrm")[0].reset();
                            }
                            
                            $('.ajax-loader').css("visibility", "hidden");
                            $("#event_btn").prop('disabled', false);
                            $(".addevent-messages").css("display", "block");
                            $('.addevent-messages .error-messages').html(response.message); 
                            $('html, body').animate({ scrollTop: 0 }, 'slow');
                            setTimeout(function () {
                                $(".addevent-messages, .addevent-error").css("display", "none");
                            }, 5000);
                        } else {
                            $('.ajax-loader').css("visibility", "hidden");
                            $("#event_btn").prop('disabled', false);
                            $(".addevent-error").css("display", "block");
                            $('.addevent-error .error-messages').html(response.message); 
                            $('html, body').animate({ scrollTop: 0 }, 'slow');
                            setTimeout(function () {
                                $(".addevent-messages, .addevent-error").css("display", "none");
                            }, 5000);
                        }
                    }
                });
            }   
        },        

        // Add event form using frontend callback
        orgAdd: function (e) {
            e.preventDefault();
            if(!$("#addOrg").valid()) 
            {return false;}

            $("#org_btn").prop('disabled', true);
            var passData = $("#addOrg").serialize();
            passData = passData+'&action=add_organiser&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({                    
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            $("#addOrg")[0].reset();
                            $(".addorg-messages").css("display", "block");
                            $('.addorg-messages .error-messages').html(response.message); 
                            setTimeout(function () {
                                $(".addorg-messages").css("display", "none");
                                location.reload();
                            }, 5000);                            
                        } else {
                            $("#org_btn").prop('disabled', false);
                            $(".addorg-error").css("display", "block");
                            $('.addorg-error .error-messages').html(response.message);
                            $('html, body').animate({ scrollTop: 0 }, 'slow');
                            setTimeout(function () {
                                $(".addorg-error").css("display", "none");
                            }, 5000);
                        }
                    }
                });
            }   
        }, 

        /* Auto complete address callback*/
        autoAddress: function () {
            var searchInput = 'search_input';
            var autocomplete;
            var gujaratStateBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(18.76601963253767, 60.50695023045195),
                new google.maps.LatLng(24.721986610412365, 74.37169632420195)
            );
            autocomplete = new google.maps.places.Autocomplete((document.getElementById(searchInput)), {
                //types: ['address'],
                bounds: gujaratStateBounds,
                strictBounds: true,
                componentRestrictions: {
                  country: "IN"
                }       
            });
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var near_place = autocomplete.getPlace();
                document.getElementById('loc_lat').value = near_place.geometry.location.lat();
                document.getElementById('loc_long').value = near_place.geometry.location.lng();
                // Pin drop location set
                loadMap = gujChild.googlePin(near_place.geometry.location.lat(),near_place.geometry.location.lng());
                google.maps.event.addDomListener(window, 'load', loadMap);

                // document.getElementById('latitude_view').innerHTML = near_place.geometry.location.lat();
                // document.getElementById('longitude_view').innerHTML = near_place.geometry.location.lng();
            }); 
        },

        // Delete event frontend callback
        deleteEvent: function (e) {
            e.preventDefault();            
            var event_post_delete_id = $(this).attr('data-postid');
            var passData = 'ev_id='+event_post_delete_id+'&action=delete_event&nonce='+ajaxPar.gujNonce;
            if (passData) {
                if (confirm("Are you Sure want to delete?")) {
                    $.ajax({   
                        beforeSend: function(){
                            $('.ajax-loader').css("visibility", "visible");
                        },                 
                        url: ajaxPar.ajaxUrl,
                        type: 'post',
                        data: passData,
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') { 
                                $('.ajax-loader').css("visibility", "hidden");   
                                $(".del_eve_"+response.ids).css("display", "none");                              
                                $(".delete-event-messages").css("display", "block");
                                $('.delete-event-messages .error-messages').html(response.message);
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                                setTimeout(function () {
                                    $(".delete-event-messages").css("display", "none");
                                }, 5000);                            
                                // location.reload();             
                            }
                        }
                    });
                }
            }   
        }, 
        orgComm: function (e) {
            // alert();
            e.preventDefault();
                if(!$("#addComm").valid()) 
                {
                    alert();
                    return false;}
    
                $("#event_comment").prop('disabled', true);
                var passData = $("#addComm").serialize();
                passData = passData+'&action=add_comment&nonce='+ajaxPar.gujNonce;
                if (passData) {
                    $.ajax({                    
                        url: ajaxPar.ajaxUrl,
                        type: 'post',
                        data: passData,
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') {
                                $("#addComm")[0].reset();
                                $(".addcomm-messages").css("display", "block");
                                $('.addcomm-messages .error-messages').html(response.message); 
                                setTimeout(function () {
                                    $(".addcomm-messages").css("display", "none");
                                    location.reload();
                                }, 5000);                            
                            } else {
                                $("#event_comment").prop('disabled', false);
                                $(".addcomm-error").css("display", "block");
                                $('.addcomm-error .error-messages').html(response.message);
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                                setTimeout(function () {
                                    $(".addcomm-error").css("display", "none");
                                }, 5000);
                            }
                        }
                    });
                }   
            }, 
    

        /*
        *  Login user form ajax call
        */
        loginUser: function (e) {
            e.preventDefault();
            if(!$("#login_frm").valid()) 
            {return false;}

            $(".login_btn").prop('disabled', true);
            var passData = $("#login_frm").serialize();
            passData = passData+'&action=login_user_call&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({ 
                    beforeSend: function(){
                        $('.ajax-loader').css("visibility", "visible");
                    },                    
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".login_btn").prop('disabled', false); 
                            window.location = ajaxPar.sitUrl;                        
                        } else {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".login_btn").prop('disabled', false);
                            $(".login-error").css("display", "block");
                            $('.login-error .error-messages').html(response.message);
                            $('html, body').animate({ scrollTop: 0 }, 'slow');
                            setTimeout(function () {
                                $(".login-error").css("display", "none");
                            }, 5000);
                        }
                    }
                });
            }
        },
        

        /*
        *  Register user form ajax call
        */
        registerUser: function (e) {
            e.preventDefault();
            if(!$("#registration_frm").valid()) 
            {return false;}

            $(".reg_btn").prop('disabled', true);
            var passData = $("#registration_frm").serialize();
            passData = passData+'&action=register_user_call&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({ 
                    beforeSend: function(){
                        $('.ajax-loader').css("visibility", "visible");
                    },                   
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".reg_btn").prop('disabled', false);
                            $(".reg-messages").css("display", "block");
                            $('.reg-messages .error-messages').html(response.message); 
                            $("#registration_frm")[0].reset();
                            $('html, body').animate({ scrollTop: 0 }, 'slow');  
                            setTimeout(function () {
                                $(".reg-messages, .reg-error").css("display", "none");
                            }, 5000);
                            // window.location = ajaxPar.sitUrl;                       
                        } else {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".reg_btn").prop('disabled', false);
                            $(".reg-error").css("display", "block");
                            $('.reg-error .error-messages').html(response.message);
                            $('html, body').animate({ scrollTop: 0 }, 'slow');  
                            setTimeout(function () {
                                $(".reg-messages, .reg-error").css("display", "none");
                            }, 5000);
                        }
                    }
                });
            }
        },

        /*
        *  Register user form dashboard ajax call
        */
        registerDashUser: function (e) {
            e.preventDefault();
            if(!$("#dash_registration_frm").valid()) 
            {return false;}

            $(".reg_btn").prop('disabled', true);
            var passData = $("#dash_registration_frm").serialize();
            passData = passData+'&action=register_dash_user_call&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({ 
                    beforeSend: function(){
                        $('.ajax-loader').css("visibility", "visible");
                    },                   
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".reg_btn").prop('disabled', false);
                            $(".reg-messages").css("display", "block");
                            $('.reg-messages .error-messages').html(response.message); 
                            $("#dash_registration_frm")[0].reset();
                            $('html, body').animate({ scrollTop: 0 }, 'slow');  
                            setTimeout(function () {
                                $(".reg-messages, .reg-error").css("display", "none");
                            }, 5000);
                            // window.location = ajaxPar.sitUrl;                       
                        } else {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".reg_btn").prop('disabled', false);
                            $(".reg-error").css("display", "block");
                            $('.reg-error .error-messages').html(response.message);
                            $('html, body').animate({ scrollTop: 0 }, 'slow');  
                            setTimeout(function () {
                                $(".reg-messages, .reg-error").css("display", "none");
                            }, 5000);
                        }
                    }
                });
            }
        },

        /*
        *  Register user form ajax call
        */
        updateUser: function (e) {
            e.preventDefault();
            if(!$("#update_user").valid()) 
            {return false;}

            $(".update_btn").prop('disabled', true);
            var passData = $("#update_user").serialize();
            passData = passData+'&action=update_user_call&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({   
                    beforeSend: function(){
                        $('.ajax-loader').css("visibility", "visible");
                    },                 
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".update_btn").prop('disabled', false);
                            $(".update-messages").css("display", "block");
                            $('.update-messages .error-messages').html(response.message);      
                            $('html, body').animate({ scrollTop: 0 }, 'slow'); 
                            setTimeout(function () {
                                $(".update-messages").css("display", "none");
                            }, 5000);                
                        } else {
                            $('.ajax-loader').css("visibility", "hidden");
                            $(".update_btn").prop('disabled', false);
                            $(".update-error").css("display", "block");
                            $('.update-error .error-messages').html(response.message);
                            $('html, body').animate({ scrollTop: 0 }, 'slow'); 
                            setTimeout(function () {
                                $(".update-error").css("display", "none");
                            }, 5000);
                        }
                    }
                });
            }
        },


        /*
        *  Show Password
        */
        showPassword: function (e) {
            e.preventDefault();
            $(this).toggleClass("fa-eye fa-eye-slash");
            input = $(this).parent().find("input");
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }  
        },

        // Delete user frontend callback
        deleteUser: function (e) {
            e.preventDefault();            
            var user_delete_id = $(this).attr('data-userid');
            var passData = 'usr_id='+user_delete_id+'&action=delete_user&nonce='+ajaxPar.gujNonce;
            if (passData) {
                if (confirm("Are you Sure want to delete?")) {
                    $.ajax({   
                        beforeSend: function(){
                            $('.ajax-loader').css("visibility", "visible");
                        },                 
                        url: ajaxPar.ajaxUrl,
                        type: 'post',
                        data: passData,
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') { 
                                $('.ajax-loader').css("visibility", "hidden"); 
                                $(".del_usr_"+response.ids).css("display", "none");                              
                                $(".delete-user-messages").css("display", "block");
                                $('.delete-user-messages .error-messages').html(response.message);
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                                setTimeout(function () {
                                    $(".delete-user-messages").css("display", "none");
                                }, 5000);              
                            }
                        }
                    });
                }
            }   
        },
        
        // Delete Organiser frontend callback
        deleteOrganiser: function (e) {
            e.preventDefault();            
            var org_delete_id = $(this).attr('data-orgid');
            var passData = 'org_id='+org_delete_id+'&action=delete_organiser&nonce='+ajaxPar.gujNonce;
            if (passData) {
                if (confirm("Are you Sure want to delete?")) {
                    $.ajax({   
                        beforeSend: function(){
                            $('.ajax-loader').css("visibility", "visible");
                        },                 
                        url: ajaxPar.ajaxUrl,
                        type: 'post',
                        data: passData,
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') { 
                                $('.ajax-loader').css("visibility", "hidden"); 
                                $(".del_ord_"+response.ids).css("display", "none");
                                $(".delete-org-messages").css("display", "block");
                                $('.delete-org-messages .error-messages').html(response.message);
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                                setTimeout(function () {
                                    $(".delete-org-messages").css("display", "none");
                                }, 5000); 
                                // location.reload();             
                            } else {
                                $('.ajax-loader').css("visibility", "hidden");       
                                $(".cant-delete-org-error").css("display", "block");
                                $('.cant-delete-org-error .error-messages').html(response.message);
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                                setTimeout(function () {
                                    $(".cant-delete-org-error").css("display", "none");
                                }, 5000); 
                            }
                        }
                    });
                }
            }   
        },

        // pin drop location
        pinDropLocation: function (e) {
            e.preventDefault(); 
            var loc_lat     = $('#loc_lat').val();
            var loc_long    = $('#loc_long').val();
            if( loc_lat.length === 0 && loc_long.length === 0)
            {
                $( "#search_input" ).focus();
                alert('Please add above location');
            } else {
                $('#google_pin_drop_location').toggle();
            }

        },

        googlePin: function (google_lat,google_long) {
            //console.log(google_lat);
            //console.log(google_long);
            var latLng = new google.maps.LatLng(google_lat,google_long)
            var mapOptions = {
                center: latLng,
                zoom: 14,
                zoomControl: true,
                fullscreenControl: true,
                scrollwheel: true,
                draggable: true,
                scaleControl: false,
                disableDefaultUI: true,
                keyboardShortcuts: false,
                clickable: false,
                minZoom: 7, 
                // maxZoom: 8
                //styles: is_map_style
            }
            var mapElement = document.getElementById('google_pin_drop_location');
            var map = new google.maps.Map(mapElement, mapOptions);
            marker = new google.maps.Marker({
                position: latLng,
                map,
                draggable:true,
            });
            marker.setMap(map);
            map.setZoom(16);
            map.setCenter(marker.getPosition());
            var geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(marker, 'dragend', function() {
                geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                  if (results[0]) {
                    document.getElementById('loc_lat').value    = marker.getPosition().lat();
                    document.getElementById('loc_long').value   = marker.getPosition().lng();
                    }
                }
                });
            });
        },
        /*
        *  All event filters callback
        */
        allEventFilters: function (e) {
            e.preventDefault();

            var passData = jQuery('#filter-all-event').serialize();
            passData = passData+'&action=all_event_filters&nonce='+ajaxPar.gujNonce;
            if (passData) {  
                $.ajax({
                    beforeSend: function(){
                        $('.ajax-loader').css("visibility", "visible");
                    },
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    dataType: 'JSON',
                    success: function (response) {
                        // console.log(responce.html);
                        // response = JSON.parse(response);
                        if ( response.status === 'success' ) {
                            // $('.data-tables').DataTable();
                            // console.log(response.html);
                            $('.filter_table').html(response.html)
                            $('.data-tables').DataTable({ 
                              "destroy": true, //use for reinitialize datatable
                           });
                        } else {        
                            $('.ajax-loader').css("visibility", "hidden");                    
                            $(".filter-event-error").css("display", "block");
                            $('.filter-event-error .error-messages').html(response.message); 
                            $('html, body').animate({ scrollTop: 150 }, 'slow');
                            setTimeout(function () {
                                $(".filter-event-error").css("display", "none");
                            }, 5000);
                        }
                    },
                    complete: function(){
                        $('.ajax-loader').css("visibility", "hidden");
                    }
                });
            }
        },

    }; // end gujChild

    $(document).ready(gujChild.init);

} // end gujChildWrapper()

gujChildWrapper(jQuery);

