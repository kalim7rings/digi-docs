<?php $__env->startSection('after_css'); ?>
    <style>
        .dropzone{
            border: 2px dashed #0087F7;
            border-radius: 5px;
            background: white;
        }
        .upload-icon{
            font-size: 35px;
        }
        .dropzone .dz-preview{
            margin: 0px 50px;
        }

        .dz-error-message{
            top: -69px !important;
            display: none;
        }
        .dropzone .dz-preview.dz-error:hover .dz-error-message{
            opacity: 0 !important;
        }
        .dz-error-message:after{
            border-top: 6px solid #be2626 !important;
            border-bottom: none !important;
            top: 51px !important;
            display: none;
        }

        .btn-save{
            padding: 5px 13px;
            font-size: 13px;
            margin-top: 10px;
        }

        .add-more-btn
        {
            border-radius: 8px;
            padding: 28px 39px;
            margin-top: 6px;
            position: relative;
            display: inline-block;
            vertical-align: top;
            font-size: 38px;
            border: 1px dashed #b1b0b0;
            margin-left: 56px;
        }
        .btn-view{
            padding: 4px 18px;
            font-size: 14px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!empty(session_get('sessionKey'))): ?>
      <div class="container-fluid main_content">
        <div class="row">
            <div class="col-sm-12 col-lg-12 col-md-12 main_right_box">

            <div class="card_inner">
                <?php if( !empty($cust_details['STATUS']) && $cust_details['STATUS'] == 'COMPLETED'): ?>
                        <div class="inner_title" style="text-align: center"> <br>Thank you for fulfilling Document/Queries. <br><br>
                            <a href="<?php echo e(url('logout')); ?>">Click here</a> to Logout <br><br><br>
                            </div>
                        </div>

                <?php else: ?>
                        <div class="inner_title">
                            <?php if(!empty($cust_details['FILE_NO'])): ?>
                                Upload Documents for a faster loan approval for HDFC Loan Account No : <?php echo e($cust_details['FILE_NO']); ?> <br>
                                    
                                    <div class="note_text margin-top5 hide"> Note: You can either submit downloaded bank statement or directly submit through logging to your Netbanking Account
                                    </div>
                            <?php else: ?>
                                Upload Documents for a faster loan approval
                                <div class="note_text margin-top5 hide">
                                    Note: You can submit downloaded bank statement.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="note_text mt-2">
                            <span class="mt-4">
                                For faster processing of your loan application please upload a pdf/jpeg file of your income related documents and net downloaded bank statements. This link may be used to upload:
                            </span>

                            <ul class="ml-4 mt-3 ">
                                <li> Last 3 months’ salary slips for all applicants </li>
                                <li> Form 16 for all salaried customers </li>
                                <li> Last 6 months’ Bank statements, showing salary credits </li>
                                <li> Last 6 months’ Bank statements of all other ACTIVE bank accounts operated by you </li>
                                <li> Other documents like Appointment/ CTC letter, Increment letter, documents related to Property, etc. </li>
                            </ul>
                        </div>

                    <div class="row margin-top30 text-center">
                        <div class="col-lg-12">
                            Kindly upload the documents here.
                        </div>
                        <div class="col-lg-12 margin-top10">
                            <input class="btn btn-outline-primary btn-upload" data-toggle="modal" data-target="#mod-reply" type="submit" value="Upload Documents"
                                   data-doc_type="OTHER"
                                   data-cust_no="1"
                                   data-doc_ref_no=""
                                   data-doc_desc=""
                                   data-doc_sub_desc="" >
                        </div>

                        <div class="col-lg-12 note_text mt-2 margin-bottom30">
                            The file supported format type are JPEG, JPG, PNG or PDF.<br>
                            The maximum uploadable file size is 15MB.
                        </div>
                    </div>

                       <div class="row">
                          
                          

                          
                          
                       </div>
                        <?php echo $__env->make('upload', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                    <div class="row">

                        
                        

                        <?php if( count($receivedDocList)>0 ): ?>
                            <div class="card bg-light col-lg-7 col-md-7 col-sm-12 margin-top20" style="padding: 0px;">
                                <div class="card-header w-100 font-600" style="text-transform: capitalize;"> Received Files </div>
                                <div class="card-body w-100">
                                    <?php $__currentLoopData = $receivedDocList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-7">
                                                <?php if(!empty($docs['CUST_NAME'])): ?>
                                                    <?php echo e($docs['CUST_NAME']); ?> -
                                                <?php endif; ?>
                                                <?php echo e($docs['DOC_SUB_TYPE']); ?>

                                                <br><spna class="font-400 font14"> <?php echo e($docs['FILE_NAME']); ?> </spna>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-4 col-5 text-right padding-right-20">
                                                <form name="" id="frmView<?php echo e($docs['SRNO']); ?>" target="_blank" method="post" action="<?php echo e(url('view-document')); ?>">
                                                    <input type="hidden" name="lr_no" value="<?php echo e($docs['LR_NO']); ?>">
                                                    <input type="hidden" name="docs_srno" value="<?php echo e($docs['SRNO']); ?>">
                                                    <button type="submit" class="btn btn-outline-primary btn-upload btn-view">View</button>
                                                </form>

                                            </div>
                                        </div>
                                        <?php if( !$loop->last ): ?> <hr class="line"> <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>



                <?php endif; ?>

            </div>

            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="container-fluid main_content">
            <div class="row">
                <div class="col-sm-12 col-lg-12 col-md-12 main_right_box" style="height: 75vh;">
                    <div class="card_inner">
                        <?php if(!empty($invalidSession)): ?>
                            <h4 class="text-center" style="padding-top: 100px;"><?php echo e($invalidSession); ?> <a href="<?php echo e(url($randomKey)); ?>">Try Again</a></h4>
                        <?php else: ?>
                          <h4 class="text-center text-danger" style="padding-top: 100px;">Invalid url</h4>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


<?php $__env->stopSection(); ?>


<?php echo $__env->make('layout.layout', ['title'=>'Home'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>