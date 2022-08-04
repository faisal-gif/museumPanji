<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Virtual Tour - Museum Panji</title>
  <link rel="stylesheet" href="{{ asset('assets/landing/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/landing/css/bootstrap.css') }}">
  <style>
    html, body {
        width: 100%;
        height: 100%;
        overflow: hidden;
        margin: 0;
    }

    .pano-image{
      width: 100%;
      height: 100%;
    }
    #progress {
      width: 0;
      height: 5px;
      position: fixed;
      top: 0;
      background: #fff;
      -webkit-transition: opacity 0.5s ease;
      transition: opacity 0.5s ease;
    }
  
    #progress.finish {
      opacity: 0;
    }

    .template {
        display: none;
      }
  </style>
</head>
<body>
  <div id="progress"></div>
  
  <div class="position-fixed mt-3 ml-3">
    <div class="d-flex flex-row">
      <div class="card mr-2" id="area-menu" style="width: 18rem;">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h5 class="card-title">Area Menu</h5>
          </div>
          <hr/>
          <ul class="list-group mb-3">
            <li class="list-group-item template" style="cursor: pointer;" data-id="">An item</li>
          </ul>
          <a href="{{ url('') }}" class="btn btn-secondary btn-block">Beranda</a>
        </div>
      </div>
      <div>
        <button type="button" class="btn btn-light btn-lg" id="close-area-menu" aria-label="Close" data-isshow="Y">
          <span aria-hidden="true"><i class="fa fa-close"></i></span>
        </button>
      </div>
    </div>
  </div>

  <div id="desc-container" style="display:none">
  @foreach($virtuals as $key => $val)
      @if(isset($val['infospot']))
        @foreach($val['infospot'] as $ikey => $ival)
          <div id="iitem-{{$ival->id}}">
            <div class="card mb-3" style="width: 28rem;">
              <img src="{{$ival->foto}}" class="card-img-top" alt="{{$ival->judul}}">
              <div class="card-body">
                <h5 class="card-title">{{$ival->judul}}</h5>
                <p class="card-text">{{$ival->keterangan}}</p>
                <p class="card-text"><small class="text-muted">{{$ival->created_at}}</small></p>
              </div>
            </div>
          </div>
        @endforeach
      @endif
  @endforeach
  </div>

  <!-- Start Virtual Tour -->
  <div class="pano-image">
    <div id="typed"></div>
  </div>
  <!-- End Virtual Tour -->

  <!-- Start Scripts -->
  <script src="{{ asset('assets/landing/js/vendor/jquery-2.2.4.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="{{ asset('assets/landing/js/vendor/bootstrap.min.js') }}"></script>		
  <script src="{{ asset('assets/custom/js/three.min.js') }}"></script>
  <script src="{{ asset('assets/custom/js/panolens.min.js') }}"></script>
  <script src="{{ asset('https://pchen66.github.io/Panolens/js/typed.min.js') }}"></script>
  <script>
    const pannoImage = document.querySelector('.pano-image');
    var list = document.querySelector( '.list-group' );
    var listItem = document.querySelector( '.list-group-item' );
    var progressElement = document.getElementById( 'progress' );
    $(() => {
      $('#close-area-menu').click((event) => {
        var isshow = $('#close-area-menu').data('isshow');
        if(isshow == 'Y'){
          $('#close-area-menu').data('isshow', 'N');
          $('#area-menu').attr('style', 'width: 18rem;display: none;');
          $('#close-area-menu span .fa').removeClass('fa-close').addClass('fa-bars')
        }else{
          $('#close-area-menu').data('isshow', 'Y');
          $('#area-menu').attr('style', 'width: 18rem;');
          $('#close-area-menu span .fa').removeClass('fa-bars').addClass('fa-close')
        }
      })

      const viewer = new PANOLENS.Viewer({
        container: pannoImage,
        output: 'console',
        initialLookAt: new THREE.Vector3( 0, 0, 5000 )
      });
  
      const arr = @json($virtuals);
      // console.log(arr);
      let pano = [];
      arr.forEach((item, index) => {

        // Add to left panel
        var itemlist = listItem.cloneNode( true );
        itemlist.classList.remove( 'template' );
        itemlist.classList.add( 'vt-item-' + index );
        itemlist.setAttribute('data-id', item.id);
        itemlist.textContent = item.judul;
        list.appendChild( itemlist );
        $('.vt-item-'+index).data('id', item.id);

        const img = item.foto ;
        const panorama = new PANOLENS.ImagePanorama(img);
        panorama.addEventListener( 'progress', onProgress );
        panorama.addEventListener( 'enter', onEnter );
        // console.log('item_' + index, 'vt_'+item.id, typeof pano['vt_'+item.id]);
        if(typeof pano['vt_'+item.id] == 'undefined'){
          pano['vt_'+item.id] = panorama;
        }
        item.detail.forEach((ditem, dindex) => {
          // console.log('ditem_' + dindex, 'vt_'+ditem.vid, typeof pano['vt_'+ditem.vid]);
          const img2 = ditem.foto;
          const panorama2 = new PANOLENS.ImagePanorama(img2);
          if(typeof pano['vt_'+ditem.vid] == 'undefined'){
            pano['vt_'+ditem.vid] = panorama2;
          }
          let angle = new THREE.Vector3(parseInt(ditem.x_axis), parseInt(ditem.y_axis), parseInt(ditem.z_axis) );
          pano['vt_'+item.id].link( pano['vt_'+ditem.vid], angle, 600);
          if(dindex == 0){
            pano['vt_'+item.id].addEventListener( 'enter-fade-start', function(){
              // set active sidebar menu
              $('.list-group-item').removeClass('active');
              $(`li[data-id=${item.id}].list-group-item`).addClass('active');
              viewer.tweenControlCenter( angle, 12000 );
            } );
          }
        })

        item.infospot.forEach((iitem, iindex) => {
          var infospot = new PANOLENS.Infospot( 600, PANOLENS.DataImage.Icon );
          infospot.name = iitem.judul;
          infospot.setText = iitem.judul;
          infospot.addHoverElement(document.getElementById( `iitem-${iitem.id}` ), 200 );
          infospot.position.set(iitem.x_axis, iitem.y_axis, iitem.z_axis);
          pano['vt_'+item.id].add(infospot);
        })
      });

      var panoindex = 0;
      for(var key in pano){
        $('.vt-item-'+panoindex).on('click', (event) => {
          var target = $(event.target)
          var id = target.data('id')
          $('.list-group-item').removeClass('active');
          target.addClass('active')
          viewer.setPanorama( pano['vt_'+id] )
        });
        viewer.add(pano[key]);
        panoindex += 1;
      }
  
    })
  
    function onProgress ( event ) {
      progress = event.progress.loaded / event.progress.total * 100;
      progressElement.style.width = progress + '%';
      if ( progress === 100 ) {
        progressElement.classList.add( 'finish' );
      }
    }
  
    function onEnter ( event ) {
      progressElement.style.width = 0;
      progressElement.classList.remove( 'finish' );
    }
  </script>
  <!-- End Scripts -->
</body>
</html>