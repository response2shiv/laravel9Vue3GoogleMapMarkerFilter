<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingsController extends Controller
{
    public function mockUserList()
    {
        $file_path = public_path('mock.txt');
        $data_array = explode("\n", file_get_contents($file_path));
        $people = []; //$person
        $columns = explode(',', $data_array[0]);
        foreach ($data_array as $index => $data) {
            if ($index == 0)
                continue;
            if ($data) {
                $singleDataArray = explode(',', $data);
                $person = [];
                foreach ($columns as $key => $column) {
                    $person[$column] = $singleDataArray[$key];
                }
                $people[] = $person;
            }
        }
        return view('listings', ['people' => $people]);
    }

    public function peopleList(Request $request)
    {

        // return response()->json(['data' => $request->all()]);

        $file_path = public_path('mock.txt');
        $data_array = explode("\n", file_get_contents($file_path));
        $people = []; //$person
        $columns = explode(',', $data_array[0]);
        foreach ($data_array as $index => $data) {
            if ($index == 0)
                continue;
            if ($data) {
                $singleDataArray = explode(',', $data);
                $person = [];
                foreach ($columns as $key => $column) {
                    $person[$column] = $singleDataArray[$key];
                }
                $people[] = $person;
            }
        }
        if (count($people) > 0)
            return response()->json(['success' => true, 'data' => $people]);
        else
            return response()->json(['success' => false, 'message' => "people data computation from mock.txt file failed."]);
    }
}
