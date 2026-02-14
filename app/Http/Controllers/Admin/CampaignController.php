<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CampaignReview;
use App\Models\Campaign;
use Image;
use Toastr;
use Str;
use File;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $show_data = Campaign::orderBy('id','DESC')->get();
        return view('backEnd.campaign.index',compact('show_data'));
    }
    public function create()
    {
        $products = Product::where(['status'=>1])->select('id','name','status')->get();
        return view('backEnd.campaign.create',compact('products'));
    }
    // public function store(Request $request)
    // {
    //     // dd($request->all());
    //     $this->validate($request, [
    //         'short_description' => 'required',
    //         'description' => 'required',
    //         'name' => 'required',
    //         'status' => 'required',
    //     ]);

    //     $input = $request->except(['files']);
    //     // image one
    //     $image1 = $request->file('image_one');
    //     $name1 =  time().'-'.$image1->getClientOriginalName();
    //     $name1 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name1);
    //     $name1 = strtolower(preg_replace('/\s+/', '-', $name1));
    //     $uploadpath1 = 'uploads/campaign/';
    //     $image1Url = $uploadpath1.$name1;
    //     $img1=Image::make($image1->getRealPath());
    //     $img1->encode('webp', 90);
    //     $width1 = '';
    //     $height1 = '';
    //     $img1->height() > $img1->width() ? $width1=null : $height1=null;
    //     $img1->resize($width1, $height1, function ($constraint) {
    //         $constraint->aspectRatio();
    //     });
    //     $img1->save($image1Url);


    //     // image two
    //     $image2 = $request->file('image_two');
    //     if($image2){
    //         $name2 =  time().'-'.$image2->getClientOriginalName();
    //         $name2 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name2);
    //         $name2 = strtolower(preg_replace('/\s+/', '-', $name2));
    //         $uploadpath2 = 'uploads/campaign/';
    //         $image2Url = $uploadpath2.$name2;
    //         $img2=Image::make($image2->getRealPath());
    //         $img2->encode('webp', 90);
    //         $width2 = '';
    //         $height2 = '';
    //         $img2->height() > $img2->width() ? $width2=null : $height2=null;
    //         $img2->resize($width2, $height2, function ($constraint) {
    //             $constraint->aspectRatio();
    //         });
    //         $img2->save($image2Url);
    //     }

    //     // image three
    //     $image3 = $request->file('image_three');
    //     if($image3){
    //         $name3 =  time().'-'.$image3->getClientOriginalName();
    //         $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name3);
    //         $name3 = strtolower(preg_replace('/\s+/', '-', $name3));
    //         $uploadpath3 = 'uploads/campaign/';
    //         $image3Url = $uploadpath3.$name3;
    //         $img3=Image::make($image3->getRealPath());
    //         $img3->encode('webp', 90);
    //         $width3 = '';
    //         $height3 = '';
    //         $img3->height() > $img3->width() ? $width3=null : $height3=null;
    //         $img3->resize($width3, $height3, function ($constraint) {
    //             $constraint->aspectRatio();
    //         });
    //         $img3->save($image3Url);
    //     }

    //     dd($request->all());

    //     $input['slug'] = strtolower(Str::slug($request->name));
    //     // $campaign = Campaign::create($input);

    //     $images = $request->file('image');
    //     if($images){
    //         foreach ($images as $key => $image) {
    //             $name =  time().'-'.$image->getClientOriginalName();
    //             $name = strtolower(preg_replace('/\s+/', '-', $name));
    //             $uploadPath = 'uploads/campaign/';
    //             $image->move($uploadPath,$name);
    //             $imageUrl =$uploadPath.$name;

    //             $pimage             = new CampaignReview();
    //             $pimage->campaign_id = $campaign->id;
    //             $pimage->image      = $imageUrl;
    //             $pimage->save();
    //         }

    //     }

    //     Toastr::success('Success','Data insert successfully');
    //     return redirect()->route('campaign.index');
    // }
    public function store(Request $request)
    {
        $this->validate($request, [
            'short_description' => 'required',
            'description'       => 'required',
            'name'              => 'required',
            'status'            => 'required',
            'image_one'         => 'required|image|mimes:jpeg,png,jpg,webp',
            'banner'            => 'required|image|mimes:jpeg,png,jpg,webp',
            'image'             => 'nullable|array',
            'image.*'           => 'image|mimes:jpeg,png,jpg,webp',
        ]);

        // Upload main images
        $uploadPath = 'uploads/campaign/';

        $imageOne      = $request->file('image_one');
        $imageOneName  = strtolower(preg_replace('/\s+/', '-', time() . '-' . preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $imageOne->getClientOriginalName())));
        $imageOnePath  = $uploadPath . $imageOneName;
        $img1 = Image::make($imageOne->getRealPath())->encode('webp', 90);
        $img1->height() > $img1->width() ? $img1->resize(null, 800, function($constraint){ $constraint->aspectRatio(); })
                                        : $img1->resize(800, null, function($constraint){ $constraint->aspectRatio(); });
        $img1->save($imageOnePath);

        // Optional images
        $imageTwoPath   = null;
        $imageThreePath = null;

        if ($request->hasFile('image_two')) {
            $imageTwo      = $request->file('image_two');
            $imageTwoName  = strtolower(preg_replace('/\s+/', '-', time() . '-' . preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $imageTwo->getClientOriginalName())));
            $imageTwoPath  = $uploadPath . $imageTwoName;
            $img2 = Image::make($imageTwo->getRealPath())->encode('webp', 90);
            $img2->height() > $img2->width() ? $img2->resize(null, 800, function($constraint){ $constraint->aspectRatio(); })
                                            : $img2->resize(800, null, function($constraint){ $constraint->aspectRatio(); });
            $img2->save($imageTwoPath);
        }

        if ($request->hasFile('image_three')) {
            $imageThree      = $request->file('image_three');
            $imageThreeName  = strtolower(preg_replace('/\s+/', '-', time() . '-' . preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $imageThree->getClientOriginalName())));
            $imageThreePath  = $uploadPath . $imageThreeName;
            $img3 = Image::make($imageThree->getRealPath())->encode('webp', 90);
            $img3->height() > $img3->width() ? $img3->resize(null, 800, function($constraint){ $constraint->aspectRatio(); })
                                            : $img3->resize(800, null, function($constraint){ $constraint->aspectRatio(); });
            $img3->save($imageThreePath);
        }

        // Upload banner
        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $banner      = $request->file('banner');
            $bannerName  = strtolower(preg_replace('/\s+/', '-', time() . '-' . preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $banner->getClientOriginalName())));
            $bannerPath  = $uploadPath . $bannerName;
            $imgB = Image::make($banner->getRealPath())->encode('webp', 90);
            $imgB->resize(1200, null, function($constraint){ $constraint->aspectRatio(); });
            $imgB->save($bannerPath);
        }

        // Create campaign manually
        $campaign = new Campaign();
        $campaign->name              = $request->name;
        $campaign->slug              = Str::slug($request->name);
        $campaign->product_id        = $request->product_id; // ← add this
        $campaign->short_description = $request->short_description;
        $campaign->description       = $request->description;
        $campaign->review            = $request->review;
        $campaign->status            = $request->status;
        $campaign->image_one         = $imageOnePath;
        $campaign->image_two         = $imageTwoPath;
        $campaign->image_three       = $imageThreePath;
        $campaign->save();

        // Handle multiple review images
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $imageName = strtolower(preg_replace('/\s+/', '-', time() . '-' . $image->getClientOriginalName()));
                $imagePath = $uploadPath . $imageName;
                $image->move($uploadPath, $imageName);

                CampaignReview::create([
                    'campaign_id' => $campaign->id,
                    'image'       => $imagePath,
                ]);
            }
        }

        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('campaign.index');
    }


    public function edit($id)
    {
        $edit_data = Campaign::with('images')->find($id);
        $select_products = Product::where('campaign_id',$id)->get();
        $show_data = Campaign::orderBy('id','DESC')->get();
        $products = Product::where(['status'=>1])->select('id','name','status')->get();
        return view('backEnd.campaign.edit',compact('edit_data','products','select_products'));
    }

    public function update(Request $request)
    { $this->validate($request, [
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);
        // image one
        $update_data = Campaign::find($request->hidden_id);
        $input = $request->except('hidden_id','product_ids','files','image');
        $image_one = $request->file('image_one');
        if($image_one){
            // image with intervention
            $image_one = $request->file('image_one');
            $name1 =  time().'-'.$image_one->getClientOriginalName();
            $name1 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name1);
            $name1 = strtolower(preg_replace('/\s+/', '-', $name1));
            $uploadpath1 = 'uploads/campaign/';
            $imageUrl1 = $uploadpath1.$name1;
            $img1 = Image::make($image_one->getRealPath());
            $img1->encode('webp', 90);
            $width1 = '';
            $height1 = '';
            $img1->height() > $img1->width() ? $width1=null : $height1=null;
            $img1->resize($width1, $height1, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img1->save($imageUrl1);
            $input['image_one'] = $imageUrl1;
            File::delete($update_data->image_one);
        }else{
            $input['image_one'] = $update_data->image_one;
        }
        // image two
        $image_two = $request->file('image_two');
        if($image_two){
            // image with intervention
            $image_two = $request->file('image_two');
            $name2 =  time().'-'.$image_two->getClientOriginalName();
            $name2 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name2);
            $name2 = strtolower(preg_replace('/\s+/', '-', $name2));
            $uploadpath2 = 'uploads/campaign/';
            $imageUrl2 = $uploadpath2.$name2;
            $img2=Image::make($image_two->getRealPath());
            $img2->encode('webp', 90);
            $width2 = '';
            $height2 = '';
            $img2->height() > $img2->width() ? $width2=null : $height2=null;
            $img2->resize($width2, $height2, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img2->save($imageUrl2);
            $input['image_two'] = $imageUrl2;
            File::delete($update_data->image_two);
        }else{
            $input['image_two'] = $update_data->image_two;
        }
        // image three
        $image_three = $request->file('image_three');
        if($image_three){
            // image with intervention
            $image_three = $request->file('image_three');
            $name3 =  time().'-'.$image_three->getClientOriginalName();
            $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name3);
            $name3 = strtolower(preg_replace('/\s+/', '-', $name3));
            $uploadpath3 = 'uploads/campaign/';
            $imageUrl3 = $uploadpath3.$name3;
            $img3 = Image::make($image_three->getRealPath());
            $img3->encode('webp', 90);
            $width3 = '';
            $height3 = '';
            $img3->height() > $img3->width() ? $width3=null : $height3=null;
            $img3->resize($width3, $height3, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img3->save($imageUrl3);
            $input['image_three'] = $imageUrl3;
            File::delete($update_data->image_three);
        }else{
            $input['image_three'] = $update_data->image_three;
        }
        // image four
        $input['slug'] = strtolower(Str::slug($request->name));
        $update_data = Campaign::find($request->hidden_id);
        $update_data->update($input);

        $images = $request->file('image');
        if($images){
            foreach ($images as $key => $image) {
                $name =  time().'-'.$image->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $uploadPath = 'uploads/campaign/';
                $image->move($uploadPath,$name);
                $imageUrl =$uploadPath.$name;

                $pimage             = new CampaignReview();
                $pimage->campaign_id = $update_data->id;
                $pimage->image      = $imageUrl;
                $pimage->save();
            }
        }

        Toastr::success('Success','Data update successfully');
        return redirect()->route('campaign.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Campaign::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Campaign::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {

        $delete_data = Campaign::find($request->hidden_id);
        $delete_data->delete();

        $campaign = Product::whereNotNull('campaign_id')->get();
        foreach($campaign as $key=>$value){
            $product = Product::find($value->id);
            $product->campaign_id = null;
            $product->save();
        }
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    public function imgdestroy(Request $request)
    {
        $delete_data = CampaignReview::find($request->id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
