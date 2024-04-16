<?php

namespace App\Http\Controllers;

use App\Http\Requests\DevicesCategories\StoreRequest;
use App\Http\Requests\DevicesCategories\UpdateRequest;

use App\Models\DevicesCategories;
use Illuminate\Http\Request;

class DevicesCategoriesController extends Controller
{

    public function DevicesCategoriesList(Request $request)
    {
        return DevicesCategories::where("company_id", request("company_id") ?? 0)->orderBy("name", "asc")->get();
    }
    public function index()
    {
        return DevicesCategories::where("company_id", request("company_id") ?? 0)->orderBy("name", "asc")->paginate(request("per_page") ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $exists = DevicesCategories::where("company_id", $request->company_id)->where('name', $request->name)->exists();

            // Check if the Device number already exists
            if ($exists) {
                return $this->response('Device Category already exists.', null, true);
            }

            $record = DevicesCategories::create($request->validated());



            if ($record) {
                return $this->response('Device Category Successfully created.', $record, true);
            } else {
                return $this->response('Device Category cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function create(Request $request)
    {
        try {
            $name = $request->name;
            $exists = DevicesCategories::where("company_id", $request->company_id)->where('name', $request->name)->exists();

            // Check if the Device number already exists
            if ($exists) {
                return $this->response('Device Category already exists.', null, true);
            }

            $record = DevicesCategories::create(["name" =>  $name, "company_id" =>  $request->company_id]);

            try {
                if ($request->file_normal) {
                    $request->file_normal->move(public_path("monitor_icons/"), $record . '_normal.png');
                }
            } catch (\Exception $e) {
            }

            try {
                if ($request->file_warning) {

                    $request->file_warning->move(public_path("monitor_icons/"), $record . '_warning.png');
                }
            } catch (\Exception $e) {
            }

            if ($record) {
                return $this->response('Device Category Successfully created.', $record, true);
            } else {
                return $this->response('Device Category cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Device  $Device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DevicesCategories $DevicesCategories)
    {


        $name = $request->name;



        // If the Device number is different from the updated value

        $exists = DevicesCategories::where("company_id", $request->company_id)->where("id", "!=", $request->id)->where('name', $name)->exists();

        // Check if the DevicesCategories number already exists
        if ($exists) {
            return $this->response('Device Category already exists.', null, true);
        }



        try {


            $record = $DevicesCategories->where("id", $request->id)->update(["name" =>  $name]);



            //$filename = $request->file->getClientOriginalName();
            try {
                if ($request->file_normal) {
                    $request->file_normal->move(public_path("monitor_icons/"), $request->id . '_normal.png');
                }
            } catch (\Exception $e) {
            }

            try {
                if ($request->file_warning) {

                    $request->file_warning->move(public_path("monitor_icons/"), $request->id . '_warning.png');
                }
            } catch (\Exception $e) {
            }

            return $this->response('Device Category successfully updated.', $record, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Device  $Device
     * @return \Illuminate\Http\Response
     */

    // public function destroy(DevicesCategories $DevicesCategories)
    // {


    //     try {
    //         if ($DevicesCategories->delete()) {
    //             return $this->response('Device Category successfully deleted.', null, true);
    //         } else {
    //             return $this->response('Device Category cannot delete.', null, false);
    //         }
    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }
    public function destroy($id)
    {


        try {
            if (DevicesCategories::where("id", $id)->delete()) {
                return $this->response('Device Category successfully deleted.', null, true);
            } else {
                return $this->response('Device Category cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
