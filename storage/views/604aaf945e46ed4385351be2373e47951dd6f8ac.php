<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title or ''); ?></title>
    <link style="text/css" rel="stylesheet" href="<?php echo e(url(mix('/css/app.css', 'public'))); ?>">
    <script src="<?php echo e(url(mix('/js/app.js', 'public'))); ?>" type="text/javascript"></script>
    <?php echo $__env->yieldContent('after_css'); ?>
</head>
<body>

<?php echo $__env->make('layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->yieldContent('content'); ?>
<?php echo $__env->make('layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->yieldContent('after_script'); ?>
<script>
    var app_path = '<?php echo e(url()); ?>';
</script>
</body>
</html>