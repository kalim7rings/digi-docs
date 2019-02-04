<div id="mod-reply" tabindex="-1" role="dialog" style="" class="modal fade">
    <div class="modal-dialog modal-lg mt-5">
        <div class="modal-content">
            <div class="modal-header">
                <div class="inner_title border-0 padding0">
                    <span class="doc_sub_head"></span>
                    <span class="font-400 font14 doc_sub_desc">
                    </span>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row margin-bottom30 upload-main">
                    <div class="col-lg-12 col-md-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                                   role="tab" aria-controls="nav-home" aria-selected="true">Through Netbanking</a>
                                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                                   role="tab" aria-controls="nav-profile" aria-selected="false">From Storage</a>
                            </div>
                        </nav>
                        <div class="tab-content borderbox" id="nav-tabContent">
                            <div class="per-msg p-2 text-danger text-center"></div>
                            <div class="tab-pane fade pt-3 show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                Your will be redirected to Bank Netbanking This facility is powered by Perfios.<br><br>
                                <form id="frmPerfios" name="frmPerfios" method="post" target="perfios_popup" action="<?php echo e(url('perfios-request')); ?>" onsubmit="if(!this.terms.checked){document.getElementById('term-msg').innerHTML = 'You must agree to the terms first.';return false}else{ window.open('about:blank','perfios_popup','width=1000,height=800');}">
                                    <input type="hidden" name="doc_sr_no" value="32164">
                                    <input type="hidden" name="doc_ref_tab" value="ADDITIONAL_DOC">

                                    <div class="row margin-top10 text-center">
                                        <div class="col-lg-12 col-sm-12 col-md-12" style="font-size: 12px;">
                                            <div class="checkbox">
                                                <label><input type="checkbox" id="terms" name="terms" class="form-control1" >
                                                I accept terms and conditions.</label>
                                            </div>
                                            <div id="term-msg" class="text-danger"></div>
                                        </div>
                                    </div>


                                    <div class="row margin-top30">
                                        <div class="col-lg-12 text-center">
                                            <button class="btn btn-primary btn-submit" type="submit">Continue</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <div class="local-upload"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="frmUploadExtraData">
                    <input type="hidden" name="lr_no" value="<?php echo e(session_get('lrNo')); ?>">
                    <input type="hidden" name="lr_contact_srno" value="<?php echo e(session_get('contactSrNo')); ?>">
                    <input type="hidden" name="file_source" value="LOCAL">
                    <input type="hidden" name="file_name" value="">
                    <input type="hidden" name="additional_info1" value="">
                    <input type="hidden" name="additional_info2" value="">
                    <input type="hidden" name="additional_info3" value="">
                    <input type="hidden" name="doc_status" value="UPLOAD">
                    <input type="hidden" name="uploaded_by" value="<?php echo e(session_get('customerName')); ?>">
                </form>

                <div class="text-center">
                    <div class="thankyou_icon" style="display: none">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </div>
                    <div class="thankyou_icons active" style="display: none">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                    <div class="msg text-danger"></div>
                </div>
            </div>
        </div>
    </div>
</div>
