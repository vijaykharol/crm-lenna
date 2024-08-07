jQuery(document).ready(function(){
    jQuery(document).on('click', '#crm-login-btn', function(e){
        e.preventDefault();
        let logincheck = true;
        var username = jQuery('#username-email').val();
        if(username == ''){
            logincheck = false;
            jQuery('#username-email').css('outline', '1px solid red');
            jQuery('.username-email-error').remove();
            jQuery('#username-email').after('<span class="error username-email-error" style="display:block; color:red;">Please fill out this field.</span>');
            jQuery('#username-email').focus();
        }else{
            jQuery('.username-email-error').remove();
            jQuery('#username-email').css('outline', 'none');
        }

        var password = jQuery('#password').val();
        if(password == ''){
            logincheck = false;
            jQuery('#password').css('outline', '1px solid red');
            jQuery('.password-error').remove();
            jQuery('#password').after('<span class="error password-error" style="display:block; color:red;">Please fill out this field.</span>');
            jQuery('#password').focus();
        }else{
            jQuery('.password-error').remove();
            jQuery('#password').css('outline', 'none');
        }

        if(logincheck){
            jQuery('#crm-login-btn').html('<img src="'+ajax_object.theme_dir+'/images/crmloader.gif" class="loader" height="25" width="25">');
            var logindata = new FormData(jQuery('#crm-login-frm')[0]);
            logindata.append('action', 'crm_login_process');
            jQuery.ajax({
                url             :   ajax_object.ajax_url,
                type            :   'POST',
                data            :   logindata,
                processData     :   false, 
                contentType     :   false,
                success: function(response){
                    let res         =   JSON.parse(response);
                    let status      =   res.status;
                    let message     =   res.message;
                    if(status){
                        //hide/empty error
                        jQuery('#l-error').html('');
                        jQuery('#l-error').hide();

                        //show success
                        jQuery('#l-success').html('');
                        jQuery('#l-success').html(message);
                        jQuery('#l-success').show();

                        setTimeout(() => {
                            window.location.href= res.url;
                        }, 2000);
                    }else{
                        //hide/empty error
                        jQuery('#l-success').html('');
                        jQuery('#l-success').hide();

                        //show success
                        jQuery('#l-error').html('');
                        jQuery('#l-error').html(message);
                        jQuery('#l-error').show();

                    }
                    jQuery('#crm-login-btn').html('Sign In');
                }
            });
        }
    });
});

//dashboard repeater
jQuery(document).ready(function(){
    let index = new Date().getTime();
    jQuery('#add-button').click(function(){
        let newItem = jQuery('.repeater-item:first').clone();
        newItem.find('input').val('');
        newItem.find('input').prop('readonly', false);
        newItem.find('select').val('');
        newItem.find('input').attr('name', 'entities[' + index + '][name]');
        newItem.find('select').attr('name', 'entities[' + index + '][type]');
        jQuery('.repeater-container').append(newItem);
        index++;
    });

    jQuery(document).on('click', '.remove-button', function(){
        jQuery(this).closest('.repeater-item').remove();
    });

    //create a client
    jQuery(document).on('click', 'button#create-a-client-btn', function(e){
        e.preventDefault();
        let statuscheck = true;
        var anc_firstname = jQuery('#anc_firstname').val();
        if(anc_firstname == ''){
            statuscheck = false;
            jQuery('#anc_firstname').css('outline', '1px solid red');
            jQuery('.anc_firstname-error').remove();
            jQuery('#anc_firstname').after('<span class="error anc_firstname-error" style="display:block; color:red;">Please fill out this field.</span>');
            jQuery('#anc_firstname').focus();
        }else{
            jQuery('.anc_firstname-error').remove();
            jQuery('#anc_firstname').css('outline', 'none');
        }
        var anc_lastname = jQuery('#anc_lastname').val();
        if(anc_lastname == ''){
            statuscheck = false;
            jQuery('#anc_lastname').css('outline', '1px solid red');
            jQuery('.anc_lastname-error').remove();
            jQuery('#anc_lastname').after('<span class="error anc_lastname-error" style="display:block; color:red;">Please fill out this field.</span>');
            jQuery('#anc_lastname').focus();
        }else{
            jQuery('.anc_lastname-error').remove();
            jQuery('#anc_lastname').css('outline', 'none');
        }
        if(statuscheck){
            jQuery('button#create-a-client-btn').html('Please wait...');
            jQuery('button#create-a-client-btn').prop('disabled', true);
            var formdataa = new FormData(jQuery('form#add-new-client')[0]);
            formdataa.append('action', 'create-a-client');
            jQuery.ajax({
                url             :   ajax_object.ajax_url,
                type            :   'POST',
                data            :   formdataa,
                processData     :   false, 
                contentType     :   false,
                success: function(response){
                    let res         =   JSON.parse(response);
                    let status      =   res.status;
                    let message     =   res.message;
                    if(status){
                        //hide/empty error
                        jQuery('#l-error').html('');
                        jQuery('#l-error').hide();

                        //show success
                        jQuery('#l-success').html('');
                        jQuery('#l-success').html(message);
                        jQuery('#l-success').show();

                        setTimeout(() => {
                            window.location.href= res.url;
                        }, 2000);
                    }else{
                        //hide/empty error
                        jQuery('#l-success').html('');
                        jQuery('#l-success').hide();

                        //show success
                        jQuery('#l-error').html('');
                        jQuery('#l-error').html(message);
                        jQuery('#l-error').show();

                    }
                    jQuery('button#create-a-client-btn').html('Generate Client Code & Close');
                }
            });
        }
    });

    //Edit client
    jQuery(document).on('click', 'button#edit-client-btn', function(e){
        e.preventDefault();
        let statuscheck = true;
        var anc_firstname = jQuery('#anc_firstname').val();
        if(anc_firstname == ''){
            statuscheck = false;
            jQuery('#anc_firstname').css('outline', '1px solid red');
            jQuery('.anc_firstname-error').remove();
            jQuery('#anc_firstname').after('<span class="error anc_firstname-error" style="display:block; color:red;">Please fill out this field.</span>');
            jQuery('#anc_firstname').focus();
        }else{
            jQuery('.anc_firstname-error').remove();
            jQuery('#anc_firstname').css('outline', 'none');
        }
        var anc_lastname = jQuery('#anc_lastname').val();
        if(anc_lastname == ''){
            statuscheck = false;
            jQuery('#anc_lastname').css('outline', '1px solid red');
            jQuery('.anc_lastname-error').remove();
            jQuery('#anc_lastname').after('<span class="error anc_lastname-error" style="display:block; color:red;">Please fill out this field.</span>');
            jQuery('#anc_lastname').focus();
        }else{
            jQuery('.anc_lastname-error').remove();
            jQuery('#anc_lastname').css('outline', 'none');
        }
        if(statuscheck){
            jQuery('button#edit-client-btn').html('Please wait...');
            jQuery('button#edit-client-btn').prop('disabled', true);
            var formdataa = new FormData(jQuery('form#edit-client')[0]);
            formdataa.append('action', 'edit-client');
            jQuery.ajax({
                url             :   ajax_object.ajax_url,
                type            :   'POST',
                data            :   formdataa,
                processData     :   false, 
                contentType     :   false,
                success: function(response){
                    let res         =   JSON.parse(response);
                    let status      =   res.status;
                    let message     =   res.message;
                    if(status){
                        //hide/empty error
                        jQuery('#l-error').html('');
                        jQuery('#l-error').hide();

                        //show success
                        jQuery('#l-success').html('');
                        jQuery('#l-success').html(message);
                        jQuery('#l-success').show();

                        setTimeout(() => {
                            window.location.href= res.url;
                        }, 2000);
                    }else{
                        //hide/empty error
                        jQuery('#l-success').html('');
                        jQuery('#l-success').hide();

                        //show success
                        jQuery('#l-error').html('');
                        jQuery('#l-error').html(message);
                        jQuery('#l-error').show();

                    }
                    jQuery('button#edit-client-btn').html('Save & Close');
                }
            });
        }
    });


    //Upload Documents
    jQuery(document).on('submit', 'form#form-document-uploader', function(e){
        e.preventDefault();
        jQuery('button#submit-documents-btn').html('Please wait...');
        jQuery('button#submit-documents-btn').prop('disabled', true);
        var formdataa = new FormData(jQuery('form#form-document-uploader')[0]);
        formdataa.append('action', 'upload-client-documents');
        jQuery.ajax({
            url             :   ajax_object.ajax_url,
            type            :   'POST',
            data            :   formdataa,
            processData     :   false, 
            contentType     :   false,
            success: function(response){
                let res         =   JSON.parse(response);
                let status      =   res.status;
                let message     =   res.message;
                if(status){
                    //hide/empty error
                    jQuery('#l-error').html('');
                    jQuery('#l-error').hide();

                    //show success
                    jQuery('#l-success').html('');
                    jQuery('#l-success').html(message);
                    jQuery('#l-success').show();

                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }else{
                    //hide/empty error
                    jQuery('#l-success').html('');
                    jQuery('#l-success').hide();

                    //show success
                    jQuery('#l-error').html('');
                    jQuery('#l-error').html(message);
                    jQuery('#l-error').show();

                }
                jQuery('button#submit-documents-btn').html('Submit Documents');
            }
        });
    });
});