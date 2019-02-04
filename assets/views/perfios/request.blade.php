@extends('layout.layout')

@section('content')
    <h4 class="text-center" style="padding: 150px 0"><i class="ion fa fa-spinner fa-spin"></i> Please wait while we redirect you to Perfios website...</h4>
    <form name='perfiosForm' id="perfiosForm" method='post' action='<?= $perfios_endpoint.'start'; ?>'>
        <input type='hidden' name='payload' value='<?= $payload; ?>'>
        <input type='hidden' name='signature' value='<?= $signature; ?>'>
    </form>
@endsection

@section('after_script')
<script type="text/javascript">
    window.onload=function(){
        var auto = setTimeout(function(){ autoRefresh(); }, 100);

        function submitform(){
            document.forms["perfiosForm"].submit();
        }

        function autoRefresh(){
            clearTimeout(auto);
            auto = setTimeout(function(){ submitform(); autoRefresh(); },4000);
        }
    }
</script>
@endsection