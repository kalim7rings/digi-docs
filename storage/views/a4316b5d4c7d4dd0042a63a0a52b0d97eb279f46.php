<?php $__env->startSection('content'); ?>
<div class="container-fluid main_content">
    <div class="row">
        <div class="col-12 col-lg-12 col-md-9 main_right_box">
            <div class="card_inner">
                    <div class="inner_title">
                       

                        Upload Documents for a faster loan approval
                        <div class="note_text hide"> Note: Once the OTP is validated you can see the details </div><br>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12 text-center">
                            <div class="font-400 font18 margin-top30">
                                Enter the OTP received on
                                <?php if(session_get('contactType') === 'M'): ?>
                                    <?php echo e(number_mask(session_get('contactDetails'))); ?>

                                <?php else: ?>
                                    <?php echo e(email_mask(session_get('contactDetails'))); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <h5 class="msg text-danger text-center margin-top20 margin-bottom20 font14"></h5>
                    <form action="<?php echo e(url('validate-otp')); ?>" id="frmOTP" name="frmOTP" method="post">
                        <input type="hidden" name="<?= $csrfNameKey ?>" value="<?= $csrfName ?>">
                        <input type="hidden" name="<?= $csrfValueKey ?>" value="<?= $csrfValue ?>">
                        <div class="row margin-top10 text-center">
                            <div class="col-lg-2 col-sm-6 col-md-6">
                                <input type="password" name="otp" class="form-control">
                            </div>
                        </div>
                        <div class="row margin-top30">
                            <div class="col-lg-12 text-center">
                                <button class="btn btn-primary btn-sbmit" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>

                    <div class="row margin-top30 margin-bottom30">
                        <div class="col-lg-12 text-center resend-div text-danger">
                            <div class="resend-mgs mb-3"></div>
                            <?php if(session_get('resend_attempts')<4): ?>
                                <button type="button" class="btn btn-outline-primary btn-resend">Resend OTP</button>
                            <?php endif; ?>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.layout', ['title'=>'Document Email'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>