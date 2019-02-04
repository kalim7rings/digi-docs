<?php if($doc_type === 'PHOTO'): ?>
    <form action="<?php echo e(url('uploadDocument')); ?>" class="dropzone" id="photo-upload" method="post" enctype="multipart/form-data">
        <div class="fallback">
            <input type="hidden" name="<?= $csrfNameKey ?>" value="<?= $csrfName ?>">
            <input type="hidden" name="<?= $csrfValueKey ?>" value="<?= $csrfValue ?>">
            <input type="file" name="files">
        </div>
    </form>
<?php else: ?>


    <div class="row">
        <div class="col-md-12 mb-3">

             
             <?php if(false): ?>
                <div class="form-group row">
                    <label for="example-text-input" class="col-3 col-form-label">DOC LIST</label>
                    <div class="col-9">
                        <select name="document_type" id="document_type" class="form-control" style="display: inline-block;">
                            <option value="" selected>select</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row" id="kyc_docs_section" style="display: none">
                    <label for="example-text-input" class="col-3 col-form-label">KYC Doc Type</label>
                    <div class="col-9">
                        <select name="additional_docs" id="additional_docs" class="form-control" style="display: inline-block;">
                            <option value="" selected>--select--</option>
                        </select>
                    </div>
                </div>
             <?php endif; ?>

                <div class="form-group row" id="password_section" style="display:none">
                    <label for="example-text-input" class="col-3 col-form-label">Password</label>
                    <div class="col-9">
                        <input type="password" name="password" id="password" class="form-control" style="display: inline-block;">
                    </div>
                </div>

        </div>
    </div>


    <form id="frm-document-upload" class="dropzone" action="<?php echo e(url('uploadDocument')); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="<?= $csrfNameKey ?>" value="<?= $csrfName ?>">
        <input type="hidden" name="<?= $csrfValueKey ?>" value="<?= $csrfValue ?>">
        <div class="dz-message needsclick">
            Drop files here or click to upload.<br>
            <span class="note needsclick"><i class="fa fa-cloud-upload upload-icon mt-2"></i></span><br>
        </div>
    </form>

    <div class="note_text mt-2">
        The file supported format type are JPEG, JPG, PNG or PDF.<br>
        The maximum uploadable file size is 15MB.
    </div>

    <div class="col-sm-5 col-md-5 col-md-offset-4 mt-3 text-center">
        <button class="btn btn-xs btn-primary mt10" style="display:none;" id="uploadButton">upload</button>
    </div>
<?php endif; ?>