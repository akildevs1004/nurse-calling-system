<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\RoomSubCategory\StoreRequest;
use App\Http\Requests\Community\RoomSubCategory\UpdateRequest;

use App\Models\Community\RoomSubCategory;

class RoomSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropDown()
    {
        return RoomSubCategory::where("company_id", request("company_id") ?? 0)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RoomSubCategory::with("room_category")->where("company_id",request("company_id") ?? 0)->paginate(request("per_page") ?? 10);
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
            $record = RoomSubCategory::create($request->validated());

            if ($record) {
                return $this->response('Room Sub Category Successfully created.', $record, true);
            } else {
                return $this->response('Room Sub Category cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, RoomSubCategory $RoomSubCategory)
    {
        $name = $request->name;

        // If the room number is different from the updated value
        if ($RoomSubCategory->name !== $name) {
            $exists = RoomSubCategory::where("company_id", $request->company_id)->where('name', $name)->exists();

            // Check if the RoomSubCategory number already exists
            if ($exists) {
                return $this->response('Room Sub Category already exists.', null, true);
            }
        }

        try {
            // If the room number is the same or it's unique, update the room
            $record = $RoomSubCategory->update($request->validated());

            return $this->response('Room Sub Category successfully updated.', $record, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */

    public function destroy(RoomSubCategory $RoomSubCategory)
    {
        try {
            if ($RoomSubCategory->delete()) {
                return $this->response('Room Sub Category successfully deleted.', null, true);
            } else {
                return $this->response('Room Sub Category cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
