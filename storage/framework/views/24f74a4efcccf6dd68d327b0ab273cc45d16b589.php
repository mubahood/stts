<?php if(Session::has('toastr')): ?>
    <?php
        $toastr     = Session::pull('toastr');
        $type       = \Illuminate\Support\Arr::get($toastr->get('type'), 0, 'success');
        $message    = \Illuminate\Support\Arr::get($toastr->get('message'), 0, '');
        $options    = json_encode($toastr->get('options', []));
    ?>
    <script>
        $(function () {
            toastr.<?php echo e($type, false); ?>('<?php echo $message; ?>', null, <?php echo $options; ?>);
        });
    </script>
<?php endif; ?><?php /**PATH C:\Users\HP ENVY\Desktop\stts\vendor\encore\laravel-admin\src/../resources/views/partials/toastr.blade.php ENDPATH**/ ?>