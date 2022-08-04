<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VirtualTour;
use App\Models\VirtualTourDetail;
use App\Models\VirtualTourInfospot;

class VirtualTourController extends Controller
{
    public function index(){
        $virtuals = VirtualTour::where('status', '1')->get();
        
        foreach($virtuals as $key => $val){
            $detail = VirtualTourDetail::where('virtual_tour_id_from', $val['id'])
            ->select('virtual_tour_details.*', 'virtual_tours.judul')
            ->leftJoin('virtual_tours', 'virtual_tour_id_to', '=', 'virtual_tours.id')
            ->get();
            $virtuals[$key]['detail'] = $detail;   

            $infospot = VirtualTourInfospot::where('virtual_tour_id_from', $val['id'])->where('status', 1)->get();
            $virtuals[$key]['infospot'] = $infospot;
        }

        return view('auth/virtual/index', [
            'virtuals' => $virtuals,
        ]);
    }

    public function portalPage(){
        $virtuals = VirtualTour::where('status', '1')->get();

        foreach($virtuals as $key => $val){
            $detail = VirtualTourDetail::where('virtual_tour_id_from', $val['id'])
            ->select('virtual_tour_details.*', 'virtual_tours.judul','virtual_tours.keterangan',
            'virtual_tours.foto', 'virtual_tours.id as vid')
            ->leftJoin('virtual_tours', 'virtual_tour_id_to', '=', 'virtual_tours.id')
            ->get();
            $virtuals[$key]['detail'] = $detail;
            
            $infospot = VirtualTourInfospot::where('virtual_tour_id_from', $val['id'])->where('status', 1)->get();
            $virtuals[$key]['infospot'] = $infospot;
        }

        // dd($virtuals);

        return view('virtual-tour', [
            'virtuals' => $virtuals,
        ]);
    }

    public function addPage(){
        $virtuals = VirtualTour::where('status', '1')->get();
        return view('auth/virtual/new', [
            'virtuals' => $virtuals,
        ]);
    }

    public function store(Request $request){
        $rules = [
            'judul' => 'required',
            'keterangan' => 'nullable',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
        $messages = [
            'judul.required' => 'Judul wajib diisi',
            'foto.required' => 'Foto wajib diisi',
            'foto.image' => 'Foto harus gambar',
            'foto.mimes' => 'Foto harus bertipe: jpeg,png,jpg,gif,svg',
            'foto.max' => 'Foto maksimal 10mb',
        ];

        $request->validate($rules, $messages);

        $virtual = new VirtualTour;

        if ($request->file('foto')) {
            $image = $request->file('foto');
            $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());
        }

        $virtual->judul = $request->judul;
        $virtual->keterangan = $request->keterangan;
        $virtual->foto = $result;
        $virtual->status = 1;
        $save = $virtual->save();
        $vid = $virtual->id;
        if($request->tujuan){
            foreach($request->tujuan as $tkey => $tval){
                $virtualdet = new VirtualTourDetail;
                $virtualdet->virtual_tour_id_from = $vid;
                $virtualdet->virtual_tour_id_to = $tval;
                $virtualdet->x_axis = $request->x[$tkey];
                $virtualdet->y_axis = $request->y[$tkey];
                $virtualdet->z_axis = $request->z[$tkey];
                $virtualdet->status = 1;
                $detsave = $virtualdet->save();
            }
        }

        if($request->ijudul){
            foreach($request->ijudul as $ikey => $ival){
                
                $virtualinfo = new VirtualTourInfospot;
                if ($request->file('ifoto')[$ikey]) {
                    $image = $request->file('ifoto')[$ikey];
                    $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());

                    $virtualinfo->foto = $result;
                }
                $virtualinfo->virtual_tour_id_from = $vid;
                $virtualinfo->judul = $request->ijudul[$ikey];
                $virtualinfo->keterangan = $request->iketerangan[$ikey];
                $virtualinfo->x_axis = $request->ix[$ikey];
                $virtualinfo->y_axis = $request->iy[$ikey];
                $virtualinfo->z_axis = $request->iz[$ikey];
                $virtualinfo->status = 1;
                $infosave = $virtualinfo->save();
            }
        }

        if($save){
            return redirect()->route('admin.virtual')->with('success', 'Berhasil menambahkan virtual tour: '.$request->judul);
        }else{
            return redirect()->route('admin.virtual')->with('error', 'Gagal menembahkan virtual tour: '.$request->judul);
        }
    }

    public function pageEdit($id){
        $virtual = VirtualTour::find($id);
        $virtuals = VirtualTour::where('status', '1')->get();
        $virtualdet = VirtualTourDetail::where('virtual_tour_id_from', $id)->get();
        $virtualinfo = VirtualTourInfospot::where('virtual_tour_id_from', $id)->where('status', 1)->get();
        return view('auth.virtual.edit', [
            'virtual' => $virtual,
            'virtuals' => $virtuals,
            'virtualdet' => $virtualdet,
            'virtualinfo' => $virtualinfo,
        ]);
    }

    public function postEdit(Request $request, $id){
        $rules = [
            'judul' => 'required|',
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
        $messages = [
            'judul.required' => 'Judul wajib diisi',
            'foto.image' => 'Foto harus gambar',
            'foto.mimes' => 'Foto harus bertipe: jpeg,png,jpg,gif,svg',
            'foto.max' => 'Foto maksimal 10mb',
        ];

        $request->validate($rules, $messages);

        $virtual = VirtualTour::find($id);

        if ($request->file('foto')) {
            $image = $request->file('foto');
            $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());

            $virtual->foto = $result;
        }

        if($virtual){
            $virtual->judul = $request->judul;
            $virtual->keterangan = $request->keterangan;
            $virtual->save();
            $vid = $virtual->id;
            $delvtdet = VirtualTourDetail::where('virtual_tour_id_from', $vid)->delete(); 
            if($request->tujuan){
                foreach($request->tujuan as $tkey => $tval){
                    $virtualdet = new VirtualTourDetail;
                    $virtualdet->virtual_tour_id_from = $vid;
                    $virtualdet->virtual_tour_id_to = $tval;
                    $virtualdet->x_axis = $request->x[$tkey];
                    $virtualdet->y_axis = $request->y[$tkey];
                    $virtualdet->z_axis = $request->z[$tkey];
                    $virtualdet->status = 1;
                    $detsave = $virtualdet->save();
                }
            }

            if($request->ijudul){
                $softdelvtinfo = VirtualTourInfospot::where('virtual_tour_id_from', $vid)->update(['status' => 0]); 
                foreach($request->ijudul as $ikey => $ival){
                    if($request->iid[$ikey]){
                        $virtualinfo = VirtualTourInfospot::find($request->iid[$ikey]);
                    }else{
                        $virtualinfo = new VirtualTourInfospot;
                    }
                    if (isset($request->file('ifoto')[$ikey])) {
                        $image = $request->file('ifoto')[$ikey];
                        $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());

                        $virtualinfo->foto = $image;
                    }
                    $virtualinfo->virtual_tour_id_from = $vid;
                    $virtualinfo->judul = $request->ijudul[$ikey];
                    $virtualinfo->keterangan = $request->iketerangan[$ikey];
                    $virtualinfo->x_axis = $request->ix[$ikey];
                    $virtualinfo->y_axis = $request->iy[$ikey];
                    $virtualinfo->z_axis = $request->iz[$ikey];
                    $virtualinfo->status = 1;
                    $infosave = $virtualinfo->save();
                }
            }

            return redirect()->route('admin.virtual')->with('success', 'Berhasil menubah data virtual tour '.$request->judul);
        }else{
            return redirect()->route('admin.virtual')->with('error', 'Tidak menemukan data virtual tour');
        }
    }

    public function delete($id){
        $virtual = VirtualTour::find($id);
        if($virtual){
            $virtual->status = 0;
            $virtual->save();
            return redirect()->route('admin.virtual')->with('success', 'Berhasil menghapus data virtual tour "'.$virtual->judul.'".');
        }else{
            return redirect()->route('admin.virtual')->with('error', 'Tidak menemukan data virtual tour');
        }
    }

    public function storedetailmodal(Request $request){
        $rules = [
            'modal_vt_id_to' => 'required',
            'modal_vt_id_from' => 'required',
            'modal_x_axis' => 'required',
            'modal_y_axis' => 'required',
            'modal_z_axis' => 'required',
        ];
        $messages = [
            'modal_vt_id_to.required' => 'Tujuan wajib diisi',
            'modal_vt_id_from.required' => 'Dari wajib diisi',
            'modal_x_axis.required' => 'X axis wajib diisi',
            'modal_y_axis.required' => 'Y axis wajib diisi',
            'modal_z_axis.required' => 'Z axis wajib diisi',
        ];

        $request->validate($rules, $messages);

        $virtualdet = new VirtualTourDetail;

        $virtualdet->virtual_tour_id_from = $request->modal_vt_id_from;
        $virtualdet->virtual_tour_id_to = $request->modal_vt_id_to;
        $virtualdet->x_axis = $request->modal_x_axis;
        $virtualdet->y_axis = $request->modal_y_axis;
        $virtualdet->z_axis = $request->modal_z_axis;
        $virtualdet->status = 1;
        $save = $virtualdet->save();

        if($save){
            return redirect()->route('admin.virtual')->with('success', 'Berhasil menambahkan virtual tour detail');
        }else{
            return redirect()->route('admin.virtual')->with('error', 'Gagal menembahkan virtual tour');
        }
    }

    public function deletedetailmodal($id){
        $virtualdet = VirtualTourDetail::find($id);
        // dd($virtualdet);
        if($virtualdet){
            $virtualdet->delete();
            return redirect()->route('admin.virtual')->with('success', 'Berhasil menghapus data virtual tour detail');
        }else{
            return redirect()->route('admin.virtual')->with('error', 'Tidak menemukan data virtual tour detail');
        }
    }
}
