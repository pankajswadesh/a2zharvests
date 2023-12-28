<?php

namespace App\Http\Controllers\Admin\Page;

use App\Model\AboutUsModel;
use App\Model\ContactUsModel;
use App\Model\TermsConditionModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function contactUsPage()
    {
        if (request()->ajax()) {
            $data = ContactUsModel::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('email', function ($data) {
                    return $data->email;
                })
                ->addColumn('phone', function ($data) {
                    return $data->phone;
                })
                ->addColumn('address', function ($data) {
                    return $data->address;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editContactUsPage', ['id' => $data->id]);
                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.page.contact_us');
    }

    public function editContactUsPage($id)
    {
        $contactById = ContactUsModel::find($id);
        return view('admin.page.contact_us_edit', compact('contactById'));
    }

    public function updateContactUsPage(Request $request)
    {
        $msg = [
            'email.required' => 'Enter Your Email.',
            'phone.required' => 'Enter Phone No.',
            'address.required' => 'Enter Address.',
        ];
        $this->validate($request, [
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ], $msg);

        $id = $request->get('id');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $address = $request->get('address');
        try {
            ContactUsModel:: where('id', $id)->update([
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ]);

            return redirect()->back()->with('success', 'Contact Us Page Updated Successfully !!!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Contact Us Page Not Updated.');
        }
    }

    public function aboutUsPage()
    {
        if (request()->ajax()) {
            $data = AboutUsModel::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    return '<img src="' . $data->image . '" width="100px" height="100px"/>';
                })
                ->addColumn('title', function ($data) {
                    return $data->title;
                })
                ->addColumn('description', function ($data) {
                    return strip_tags($data->description);
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editAboutUsPage', ['id' => $data->id]);
                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action', 'image'])
                ->toJson();
        }

        return view('admin.page.about_us');
    }

    public function editAboutUsPage($id)
    {
        $aboutById = AboutUsModel::find($id);
        return view('admin.page.about_us_edit', compact('aboutById'));
    }

    public function updateAboutUsPage(Request $request)
    {
        $msg = [
            'title.required' => 'Enter Title.',
            'description.required' => 'Enter Description.',
        ];
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ], $msg);

        $id = $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        $about_image = AboutUsModel::where('id', $id)->value('image');
        try {
            if (!empty($request->hasFile('image'))) {
                $about_image = explode('/', $about_image);
                $about_image = end($about_image);
                if ($about_image != 'avatar.png' && file_exists(public_path() . '/images/page/' . $about_image)) {
                    unlink(public_path() . '/images/page/' . $about_image);
                }
                $photo = $request->file('image');
                $imageName = str_random(6) . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $destinationPath = public_path('images/page');
                $thumb_img = Image::make($photo->getRealPath())->resize(800, 800);
                $thumb_img->save($destinationPath . '/' . $imageName, 80);
                $imageName = url('/images/page/' . $imageName);
            } else {
                $imageName = $about_image;
            }

            AboutUsModel::where('id', $id)->update([
                'image' => $imageName,
                'title' => $title,
                'description' => $description,
            ]);
            return redirect()->back()->with('success', 'About Us Added Successfully !!!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'About Us Not addded.');
        }
    }

    public function TermsConditionPage()
    {
        if (request()->ajax()) {
            $data = TermsConditionModel::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('contents', function ($data) {
                    return strip_tags($data->contents);
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editTermsConditionPage', ['id' => $data->id]);
                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.page.terms_condition');
    }

    public function editTermsConditionPage($id)
    {
        $aboutById = TermsConditionModel::find($id);
        return view('admin.page.terms_condition_edit', compact('aboutById'));
    }

    public function updateTermsConditionPage(Request $request)
    {
        $msg = [
            'contents.required' => 'Enter Terms & Conditions.',
        ];
        $this->validate($request, [
            'contents' => 'required',
        ], $msg);
        $id = $request->get('id');
        $contents = $request->get('contents');
        try {
            TermsConditionModel::where('id', $id)->update([
                'contents' => $contents,
            ]);
            return redirect()->back()->with('success', 'Terms & Conditions Updated Successfully !!!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terms & Conditions Not Update.');
        }

    }
}
