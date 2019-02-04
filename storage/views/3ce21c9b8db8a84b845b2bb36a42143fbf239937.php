<?php $__env->startSection('content'); ?>
    <div class="container-fluid main_content">
        <div class="row">
            <div class="col-sm-12 col-lg-12 col-md-12 main_right_box" style="height: 75vh;">
                <div class="card_inner">
                    <div class="text-center mt-5">
                            <h1><?=$status; ?></h1>
                    </div>
                    <h4 class="text-center pt-5"><?=$message; ?></h4>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.layout', ['title'=>'Error'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>