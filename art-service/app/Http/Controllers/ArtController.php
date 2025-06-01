<?php

namespace App\Http\Controllers;

use App\Models\Art;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ArtController extends Controller
{
    public function index()
    {
        return response()->json(Art::all());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image_url' => 'required|url',
            'public_id' => 'required|string',
        ]);

        $userId = $request->get('user_id');

        $art = Art::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $request->image_url,
            'public_id' => $request->public_id,
            'user_id' => $userId
        ]);

        return response()->json(['message' => 'Karya berhasil ditambahkan', 'data' => $art]);
    }
    
public function destroy($id, Request $request)
    {
        $userId = $request->header('X-User-ID');
        $art = Art::where('id', $id)->where('user_id', $userId)->first();

        if (!$art) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($art->public_id) {
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');

            $timestamp = time();
            $signature = sha1("public_id={$art->public_id}&timestamp={$timestamp}{$apiSecret}");

            $client = new Client();
            $client->request('POST', "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
                'form_params' => [
                    'public_id' => $art->public_id,
                    'api_key' => $apiKey,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                ]
            ]);
        }

        $art->delete();
        return response()->json(['message' => 'Deleted']);
    }

public function getByUser($user_id)
    {
        $arts = \App\Models\Art::where('user_id', $user_id)->get();
        return response()->json($arts);
    }

public function getById($id)
    {
        $art = \App\Models\Art::find($id);

        if (!$art) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($art);
    }
}
