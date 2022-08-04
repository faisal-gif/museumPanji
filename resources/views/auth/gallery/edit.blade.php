@extends('layouts.adminlte3.base')

@section('title', 'Edit Gallery')

@section('content-title', 'Edit Gallery')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{ Route('admin.gallery') }}">Gallery</a></li>
  <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection

@section('content')
<div class="row justify-content-center">
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Formulir</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form action="{{ url('admin/gallery/edit/'.$gallery->id) }}" method="post" enctype="multipart/form-data">
      @csrf
        <div class="card-body">
          <div class="form-group">
            <label for="vehicle">Judul <span class="text-danger">*</span></label>
            <input class="form-control" id="judul" name="judul" value="{{$gallery->judul}}"/>
          </div>
          <div class="form-group">
            <label for="vehicle">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan">{{$gallery->keterangan}}</textarea>
          </div>
          <div class="form-group">
            <label for="vehicle">Foto</label>
            <input type="file" class="form-control" id="foto" name="foto" accept="jpeg,jpg,png"/>
          </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
    <!-- /.card -->
  </div>
</div>

@endsection

@section('head-link')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('/assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('script')
<!-- Select2 -->
<script src="{{ asset('/assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
  $(function() {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      tags: false
    })

    $("select").on("select2:select", function (evt) {
      var element = evt.params.data.element;
      var $element = $(element);
      
      $element.detach();
      $(this).append($element);
      $(this).trigger("change");
    });
  });
</script>
@endsection