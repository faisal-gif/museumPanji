@php
$user = Auth::user();
@endphp

@extends('layouts.adminlte3.base')

@section('title', 'Gallery')

@section('head-link')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection

@section('content-title', 'Gallery')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{ Route('dashboard') }}">Gallery</a></li>
</ol>
@endsection

@section('content')
<div class="row">
  <!-- left column -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">List</h3>
        <a class="btn btn-primary btn-sm float-right" href="{{ url('admin/gallery/add') }}"><i class="fa fa-plus"></i> Add New</a>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-striped">
          <thead>
            <tr>
              <th width="1%">No</th>
              <th>Gambar</th>
              <th>Judul</th>
              <th>Deskripsi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($galleries as $gallery)
            <tr>
              <td>{{$loop->iteration}}</td>
              <td><img src="{{$gallery->foto}}" alt="{{$gallery->judul}}" class="img-thumbnail" width="100" height="100"></td>
              <td>{{$gallery->judul}}</td>
              <td>{{$gallery->keterangan}}</td>
              <td>
                <a href="{{ url('admin/gallery/edit/'.$gallery->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                <button class="btn btn-default btn-sm" onClick="showDetailModal(this)" data-gallery="{{ $gallery }}"><i class="fa fa-eye"></i></button>&nbsp;&nbsp;
                <a href="{{ url('admin/gallery/delete/'.$gallery->id) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
              </td>
            </tr>  
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>

<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Detail Gallery</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless">
          <tbody>
            <tr>
              <th width="30%">Judul</th>
              <td width="1%">:</td>
              <td id="gallery_judul"></td>
            </tr>
            <tr>
              <th width="30%">Keterangan</th>
              <td width="1%">:</td>
              <td id="gallery_keterangan"></td>
            </tr>
            <tr>
              <th width="30%">Foto</th>
              <td width="1%">:</td>
              <td>
                <img id="gallery_foto" src="" alt="" class="img-thumbnail" width="100" height="100">
              </td>
            </tr>
            <tr>
              <td id="booking_action"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal --> 
@endsection

@section('script')
<!-- DataTables  & Plugins -->
<script src="{{ asset('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
    });
  });

  function showDetailModal(elem){
    var item = $(elem).data("gallery");
    console.log(item);
    
    $("#modal #gallery_judul").html(item.judul);
    $("#modal #gallery_keterangan").html(item.keterangan);
    $("#modal #gallery_foto").attr('src', item.foto);

    $("#modal").modal('show');
  }

</script>
@endsection
     