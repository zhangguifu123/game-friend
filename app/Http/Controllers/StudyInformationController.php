<?php

namespace App\Http\Controllers;

use App\Models\StudyInformation;
use Illuminate\Http\Request;

class StudyInformationController extends Controller
{
    //
    public function upload(Request $request) {
        if (!$request->input('image') || !$request->input('name') || !$request->input('game_id') || !$request->input('manager_name') || !$request->input('manager_id')) {
            return msg(3 , __LINE__);
        }
        $data = $request->all();
        $studyInformation   = new StudyInformation($data);
        $studyInformation->save();
        return msg(0, $studyInformation);
    }

    public function delete (Request $request) {
        if (!$request->route('id')) {
            return msg(3 , __LINE__);
        }
        $studyInformation =  StudyInformation::query()->find($request->route('id'));
        if (!$studyInformation) {
            return msg(11, __LINE__);
        }
        $studyInformation->delete();
        return msg(0, __LINE__);
    }
    public function getList(Request $request) {
        if (!$request->route('page')){
            return msg(1, __LINE__);
        }
        //分页，每页10条
        $limit = 10;
        $offset = $request->route("page") * $limit - $limit;
        $studyInformation =  StudyInformation::query();
        $studyInformationSum = $studyInformation->count();
        $studyInformationList = $studyInformation
            ->limit(10)
            ->offset($offset)->orderByDesc("created_at")
            ->get()
            ->toArray();
        $message['bannerList'] = $studyInformationList;
        $message['total']    = $studyInformationSum;
        $message['limit']    = $limit;
        return msg(0, $message);
    }
}
