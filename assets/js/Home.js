import Dropzone from 'dropzone';

let allowedMaxAttempt = 4;
let passwordFlag = 1;

$(document).ready(function () {

    initAccordion();
    initValidation();

    setTimeout(function () {
        $('.thankyou_icon').addClass('active');
    }, 1000);

    $(".btn-resend").on('click', resendOTP);

    $(".btn-upload").on('click', openPopUpAndGenerateFileUploader);

    $(document).on('change','#additional_docs', function () {
        $("input[name='additional_info1']").val($(this).val());
        $('select[name="additional_docs"]').css('border','1px solid #dfdfdf').next().html('');
    });

    $(document).on('change','#document_type', generateSubDocList);

    $(document).on('keyup','#password', setPassword);

    $(document).on('click','.add-more-btn', function () {
        $('.dropzone').trigger('click');
    });

    $('#mod-reply').on('hidden.bs.modal', function () {
        window.location.reload();
    })
});

function setPassword() {
    $('input[name="additional_info2"]').val($(this).val());
    $('#password').css('border','1px solid #dfdfdf').next().html('');
    $('.per-msg').html('');
}

window.uploadPerfiosFile = function uploadPerfiosFile(perfios_trxn_no, perfios_status, perfios_reason) {

    console.log("perfios_trxn_no : " + perfios_trxn_no);
    console.log("perfios_status : " + perfios_status);
    console.log("perfios_reason : " + perfios_reason);

    if (perfios_status === 'success') {

        let form_params = $('#frmUploadExtraData').serializeArray();
        form_params[form_params.length] = {name: "perfios_trxn_no", value: perfios_trxn_no};
        $('#nav-home').hide();
        $('.per-msg').html('<i class="ion fa fa-spinner fa-spin"></i> Please Wait...');

        $.ajax(
            {
                url: app_path + 'download',
                type: 'post',
                dataType: 'json',
                data: form_params,
                success: function (resp) {
                    if (resp) {
                        if (resp['UPLOADDOCUMENT']) {
                            $('.per-msg').html(resp['UPLOADDOCUMENT'][0]['MESSAGE']);
                            return false;
                        } else if (resp['UPLOAD_DOCUMENT_DET']) {
                            if (resp['UPLOAD_DOCUMENT_DET'][0]['RETURN_CODE'] == '0') {
                                $('.local-upload').html('');
                                $('.upload-main').hide();
                                $('.thankyou_icon').show();
                                $('.msg').html('Document Uploaded Successfully.').removeClass('text-danger').addClass('text-success');
                                return false;
                            } else {
                                $('.per-msg').html(resp['UPLOAD_DOCUMENT_DET'][0]['MESSAGE']);
                                return false;
                            }
                        } else {
                            $('.per-msg').html(resp);
                            return false;
                        }
                    } else {
                        $('.per-msg').html('Error while Uploading Document.');
                        return false;
                    }
                },
                error: function (resp) {
                    $('.per-msg').html('Error while Uploading Document.');
                    return false;
                }
            });
    } else {
        $('.local-upload').html('');
        $('.upload-main').hide();
        $('.thankyou_icons').show();
        $('.msg').html(perfios_reason);
        return false;
    }
}

function initDocumentUploadDropzone() {

    if ($('#frm-document-upload').length) {
        let myDropzone = new Dropzone("#frm-document-upload", {
            autoProcessQueue: false,
            paramName: "files",
            uploadMultiple: true,
            maxFilesize: 15,  //MB
            acceptedFiles: ".jpeg,.jpg,.png,.pdf",
            parallelUploads: 10,
            maxFiles: 10,
            addRemoveLinks: true,
            timeout: 0,
            dictRemoveFile: '✘',
            dictCancelUpload: '✘',
            dictInvalidFileType : "Invalid File type. Kindly upload valid File.",

            init: function () {
                var submitButton = document.querySelector("#uploadButton");

                submitButton.addEventListener("click", function (e) {

                    if(($('#document_type').length == 1 && $('#document_type').val() == '' )|| ($('#additional_docs').is(':visible') && $('#additional_docs').val() == '') || ($('#password').is(':visible') && $('#password').val() == ''))
                    {
                        checkValidation();
                    }else{
                        $.blockUI();
                        myDropzone.processQueue();  // Tell Dropzone to process all queued files.
                    }

                });

                this.on("addedfile", function (file) {
                    //myDropzone.removeEventListeners();

                    $('.add-more-btn').remove();
                    $($('.dz-preview').last()[0]).after('<div class="add-more-btn needsclick" id="add-more-btn"> + </div>');
                    myDropzone.clickable = '#add-more-btn';
                    $('#uploadButton').show();
                });

                this.on('sending', function (file, xhr, formData) {
                    var otData = $('#frmUploadExtraData').serializeArray();
                    for (var i = 0; i < otData.length; i++)
                        formData.append(otData[i].name, otData[i].value);
                });

                this.on("removedfile", function (file) {

                    if(myDropzone.getRejectedFiles().length === 0){
                        myDropzone.setupEventListeners();
                        $('.add-more-btn').css('display','inline-block');
                        $('#uploadButton').show();
                    }

                    $('.per-msg').html('');
                    if ($('.dz-preview:not(.dz-success)').length == 0) {
                        $('.add-more-btn').remove();
                        myDropzone.setupEventListeners();
                        $('#uploadButton, #password_section').hide();
                        $('#password').val('');
                    }
                });

                this.on("success", function (files, response) {

                    if (response) {
                        $.unblockUI();
                        if (response['UPLOADDOCUMENT'])
                        {
                            $('.per-msg').html(response['UPLOADDOCUMENT'][0]['MESSAGE']);

                            return false;
                        } else if (response['UPLOAD_DOCUMENT_DET']) {
                            if (response['UPLOAD_DOCUMENT_DET'][0]['RETURN_CODE'] == '0') {
                                $(".dz-remove").css("display", "none");
                                $(".dz-error-mark").css("display", "none");
                                $(".dz-success-mark svg").css("background", "green");
                                $('#uploadButton').hide();

                                $('.local-upload').html('');
                                $('.upload-main').hide();
                                $('.thankyou_icon').show();
                                $('.msg').html('Document Uploaded Successfully.').removeClass('text-danger').addClass('text-success');
                                return false;
                            } else {
                                $('.per-msg').html(response['UPLOAD_DOCUMENT_DET'][0]['MESSAGE']);
                                return false;
                            }
                        } else {
                            $('.per-msg').html(response);
                            return false;
                        }
                    } else {
                        $('.per-msg').html('Error while Uploading Document.');
                        return false;
                    }

                });

                // this.on("errormultiple", function (files, response) {
                //     if(response['UPLOADDOCUMENT'][0]['RETURN_CODE'] == '15'){
                //         $('.per-msg').html(response['UPLOADDOCUMENT'][0]['MESSAGE']);
                //         $('#password_section').show();
                //     }
                // });

                this.on("error", function (file, response)
                {
                    if(typeof response['UPLOADDOCUMENT'] == 'undefined')
                    {
                        $('.add-more-btn').css('display','none');
                        myDropzone.removeEventListeners();
                        $('#uploadButton').hide();
                        $('.per-msg').html(response);
                        return;
                    }

                    var msg = passwordFlag === 1 ? 'The file is password protected, please enter the password in the space provided.' : 'Invalid Password.';

                    file.status = 'queued';
                    if(response['UPLOADDOCUMENT'][0]['RETURN_CODE'] == '15'){
                        $('.per-msg').html(msg);
                        $('#password_section').show();
                        $('.add-more-btn').css('display','none');
                        myDropzone.removeEventListeners();
                        $.unblockUI();
                        passwordFlag++;
                    }
                });
            }
        });
    }
}

function openPopUpAndGenerateFileUploader()
{
    $('.upload-main').show();
    $('.thankyou_icon').hide();
    $('.msg').html('');
    let $doc_type = $(this).data('doc_type');

    $("input[name='cust_no']").val($(this).data('cust_no'));
    $("input[name='doc_ref_no']").val($(this).data('doc_ref_no'));
    $("input[name='doc_type']").val($doc_type);
    $(".doc_sub_desc").html('(' + $(this).data('doc_sub_desc') + ')');
    $(".doc_sub_head").html($(this).data('doc_desc'));

    if ($doc_type === 'OTHER') {
        $('#nav-profile-tab').trigger('click');
        $('#nav-tab, #nav-home').hide();
        $(".doc_sub_head").html('Upload Documents');
        $(".doc_sub_desc").html('');
        $('#nav-tabContent').css('margin-top','-18px').removeClass('borderbox');

        setTimeout(function () {
            generateDocList('ADDITIONAL_DOC','document_type');
        }, 500);

    }

    $.ajax({
        url: app_path + 'generate-uploader',
        type: 'post',
        dataType: 'html',
        data: 'doc_type=' + $doc_type,
        success: function (resp) {
            if (resp) {
                $(".local-upload").html(resp);
                initDocumentUploadDropzone();
            } else {
                console.log('', 'Something went wrong!', 'error')
            }
        }
    });
}

function resendOTP() {

    $('.resend-mgs').html('<i class="ion fa fa-spinner fa-spin"></i> Please Wait...');

    $.ajax({
        type: "POST",
        url: app_path + 'resend-otp',
        dataType: 'json',
        success: function (resp) {
            if (resp) {
                if (resp.NO < allowedMaxAttempt) {
                    if (resp.RETURN_CODE === '0') {
                        $('.resend-mgs').html('OTP Sent Successfully.');
                    } else {
                        $('.resend-mgs').html(resp.RETURN_MESSAGE);
                    }
                } else {
                    $('.resend-div').html('Maximum no of attempts reached, Try after sometime.').fadeOut(8000);
                }
            } else {
                $('.resend-mgs').html('Something went wrong.');
            }
        },
        error: function (resp) {
            $('.resend-mgs').html('Something went wrong.');
        }
    });
}

function initAccordion() {
    let Accordion = function (el, multiple) {
        this.el = el || {};
        this.multiple = multiple || false;
        let links = this.el.find('.menu_head_title');
        links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
    };

    Accordion.prototype.dropdown = function (e) {
        let $el = e.data.el,
            $this = $(this),
            $next = $this.next();

        $next.slideToggle();
        $this.parent().toggleClass('open');

        if (!e.data.multiple) {
            $el.find('.menu_head_title').not($this).parent().removeClass('open');
        }
    };

    let accordion = new Accordion($('#accordion'), false);
}

function initValidation() {

    $("#frmOTP").validate({
        errorPlacement: function (error, element) {
            $(error).insertAfter(element);
        },
        errorClass: 'error-msg font-size10 text-danger',
        highlight: function (element) {
            $(element).removeClass('font-size10').css('border', '1px red solid');
            $(element).prev().css('color', 'red');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('font-size10').css('border', '');
            $(element).prev().css('color', '');
        },
        rules: {
            otp: {
                required: true,
                number: true,
                maxlength: 6,
                minlength: 4,
            }
        },
        messages: {
            otp: {
                number: "Enter valid OTP."
            },
        },
        submitHandler: function (form) {

            let $save = $('.btn-sbmit');
            $save.text('Please wait...').attr('disabled', true);
            $('.msg').html('');

            $.ajax({
                type: "POST",
                url: $(form).attr('action'),
                data: $(form).serialize(),
                dataType: 'json',
                success: function (resp) {
                    if (resp) {
                        if (resp.RETURN_CODE === '0') {
                            window.location = app_path + 'home';
                        } else {
                            $('.msg').html(resp.RETURN_MESSAGE);
                            $save.text('Submit').attr('disabled', false);
                        }
                    }
                },
                error: function (resp) {
                    $save.text('Submit').attr('disabled', false);
                }
            });
        }
    });

    $('form.frmPassword').each(function () {
        $(this).validate({
            errorPlacement: function (error, element) {
                $(error).insertAfter(element);
            },
            errorClass: 'error-msg font-size10 text-danger',
            highlight: function (element) {
                $(element).removeClass('font-size10').css('border', '1px red solid');
                $(element).prev().css('color', 'red');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('font-size10').css('border', '');
                $(element).prev().css('color', '');
            },
            rules: {
                password: {
                    required: true,
                }
            },
            messages: {
                password: {
                    required: "Enter  password.",
                    number: "Enter valid password."
                },
            },
            submitHandler: function (form) {
                let $save = $(form).find('.btn-save');
                $save.text('wait...').attr('disabled', true);
                $('.msg').html('');

                $.ajax({
                    type: "POST",
                    url: $(form).attr('action'),
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function (resp) {
                        if (resp) {
                            if (resp.RETURN_CODE == '0') {
                                window.location = app_path + 'home';
                            } else {
                                $('<label class="error-msg font-size10 text-danger">'+resp.MESSAGE+'</label>').insertAfter($(form).find('.password-input'));
                                $save.text('Save').attr('disabled', false);
                            }
                        }
                    },
                    error: function (resp) {
                        $save.text('Save').attr('disabled', false);
                    }
                });
            }
        });
    });

}

function generateDocList(docType, selector) {

    $.ajax({
        type: "POST",
        url: app_path + 'doc-list',
        data: {docType:docType},
        dataType: 'json',
        success: function (resp) {

            if(resp.status){

                if(selector == 'additional_docs' && resp.data.length > 0){
                    setTimeout(function () {
                        $('#kyc_docs_section').show();
                    },500);
                }

                $.each(resp.data, function (i, val) {
                    generateOptions(val.CD_VAL, val.CD_DESC, selector);
                });
                return;
            }

            if(resp.status == false){
                $('#kyc_docs_section').hide();
                makeListEmpty(selector);
            }

        },
        error: function (resp) {
           console.log('Error');
        }
    });
}

function generateSubDocList()
{
    $('select[name="document_type"]').css('border','1px solid #dfdfdf').next().html('');
    let subDocSelector = 'additional_docs';
    makeListEmpty(subDocSelector);
    $('input[name="doc_type"]').val($(this).val());
    generateDocList($(this).val(),`${subDocSelector}`);
}

function makeListEmpty(subDocSelector)
{
    $(`select[name="${subDocSelector}"]`).html('<option value="" selected>--select--</option>');
    $('input[name="additional_info1"]').val('');
}

function generateOptions(key, desc, selector)
{
    let option = `<option value="${key}">${desc}</option>`;
    $(`select[name="${selector}"]`).append(option);

}

function checkValidation()
{
    let selectorArr = ['document_type', 'additional_docs', 'password'];

    $.each(selectorArr, function (i, val) {

        let errSelector = $('select[name="'+val+'"], input[name="'+val+'"]');

        $(errSelector).css('border','1px solid #dfdfdf').next().remove();

        if($(errSelector).is(':visible') &&  $(errSelector).val() == '')
        {
            $(errSelector).css('border','1px solid red').after('<i class="text-danger">This field is required.</i>');
        }
    });
}
