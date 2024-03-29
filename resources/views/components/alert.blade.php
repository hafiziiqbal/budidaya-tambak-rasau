@if ($errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-warning"></i>
        {{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->has('alert'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fa fa-warning"></i>
        {{ $errors->first('alert') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check"></i>
        {{ session()->get('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


<div id="alertNotif" class="alert alert-success alert-dismissible fade show d-none" role="alert">
    <i class="fa fa-check"></i>
    <span></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div id="alertNotifError" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
    <i class="fa fa-warning"></i>
    <span></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
