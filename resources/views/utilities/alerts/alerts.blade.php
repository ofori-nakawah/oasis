@if(Session::has('info'))
    <div class="alert alert-info" role="alert" style="    box-shadow: 0 4px 15px 0 rgba(31, 43, 58, 0.1);border: none;">
        {{ Session::get('info') }}
    </div>
@endif

@if(Session::has('danger'))
    <div class="alert alert-danger" role="alert" style="    box-shadow: 0 4px 15px 0 rgba(31, 43, 58, 0.1);border: none;">
        {{ Session::get('danger') }}
    </div>
@endif

@if(Session::has('success'))
    <div class="alert alert-success" role="alert" style="    box-shadow: 0 4px 15px 0 rgba(31, 43, 58, 0.1);border: none;">
        {{ Session::get('success') }}
    </div>
@endif

@if(Session::has('warning'))
    <div class="alert alert-warning" role="alert" style="    box-shadow: 0 4px 15px 0 rgba(31, 43, 58, 0.1);border: none;">
        {{ Session::get('warning') }}
    </div>
@endif
