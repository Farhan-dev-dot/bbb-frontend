<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class master_customerController extends Controller
{
    public function index(Request $request)
    {

        /* ========================================================================= */
        /* redirect ke login jika token tidak ada */
        /* ========================================================================= */
        if (!session('access_token')) {
            return Redirect('login');
        }


        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');
        /* ========================================================================= */
        /* tarik data master yang dibutuhkan. contoh : lokasi, barang, */
        /* ========================================================================= */

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/master-customer', [
            'alldata' => 1,
        ]);

        $customers = $response->json()['data'];
        $searchData = $this->getData($request);
        // dd($customers);
        return view(
            'master.form-master-customer',
            [
                'data' => $searchData,
                'customer' => $customers
            ]

        );
    }

    public function getData(Request $request)
    {

        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        // Get the current page number from the query string 'page'
        $currentPage = $request->input('page', 1);

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/master-customer', [
            'page' => $currentPage,
            'custidkeyword' => $request->input('custidkeyword'),
            'custnamekeyword' => $request->input('custnamekeyword'),
            'keyword' => $request->input('keyword'),
            'sortby' => $request->input('sortby'),
            'sortorder' => $request->input('sortorder'),
            'per_page' => $request->input('per_page', 10)
        ]);


        $data = $response->json();

        $customer = $data['data'];
        $currentPage = $data['current_page'];
        $perPage = $data['per_page'];
        $total = $data['total'];
        $totalPage = $data['total_page'];
        $hasNextPage = $data['has_next_page'];
        $hasPrevPage = $data['has_prev_page'];
        $from = $data['from'];
        $to = $data['to'];

        return compact('customer', 'currentPage', 'perPage', 'total', 'totalPage', 'hasNextPage', 'hasPrevPage', 'from', 'to');
    }
    function InsertData(Request $request)
    {
        /* ========================================================================= */
        /* deklarasi token dan APIURL */
        /* ========================================================================= */
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');


        /* ========================================================================= */
        /* kirim data ke back end untuk diinsert */
        /* ========================================================================= */

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post($apiUrl . '/api/master-customer', $request->all());

        return response()->json($response->json(), $response->status());
    }


    public function getDatabyid($id)
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/master-customer/' . $id);

        return response()->json($response->json(), $response->status());
    }

    public function UpdateData(Request $request,  $id)
    {
        /* ========================================================================= */
        /* deklarasi token dan APIURL */
        /* ========================================================================= */
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        /* ========================================================================= */
        /* kirim data ke back end untuk diupdate */
        /* ========================================================================= */
        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->patch($apiUrl . '/api/master-customer/' . $id, $request->all());

        return response()->json($response->json(), $response->status());
    }


    public function DeleteData($id)
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->delete($apiUrl . '/api/master-customer/' . $id);

        return response()->json($response->json(), $response->status());
    }
}
