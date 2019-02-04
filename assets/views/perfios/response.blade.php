@extends('layout.layout')

@section('content')
@endsection

@section('after_script')
    <script type="text/javascript">
        window.onload=function(){
            window.opener.uploadPerfiosFile('<? echo $perfios_trxn_no; ?>','<? echo $perfios_status; ?>', '<? echo $perfios_reason; ?>');
            window.close();
        }
    </script>
@endsection