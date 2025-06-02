<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;

class GatewayController extends Controller
{
    public function forwardArts(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        
            $client = new Client();
            $response = $client->request('GET', 'http://art-service:8000/arts');
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid token',
                'msg' => $e->getMessage()
            ], 401);
        }
    }

    public function createArt(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $userId = $decoded->sub;

            $title = $request->input('title');
            $description = $request->input('description');
            $imageBase64 = $request->input('image_base64');

            if (!$title || !$imageBase64) {
                return response()->json(['error' => 'Title and image_base64 are required'], 422);
            }

            // Upload to Cloudinary
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');

            $timestamp = time();
            $signature = sha1("timestamp={$timestamp}{$apiSecret}");

            $client = new \GuzzleHttp\Client();
            $upload = $client->request('POST', "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                'multipart' => [
                    ['name' => 'file', 'contents' => $imageBase64],
                    ['name' => 'api_key', 'contents' => $apiKey],
                    ['name' => 'timestamp', 'contents' => $timestamp],
                    ['name' => 'signature', 'contents' => $signature],
                ]
            ]);

            $uploadResult = json_decode($upload->getBody(), true);

            // Kirim ke art-service
            $artResponse = $client->request('POST', 'http://art-service:8000/arts', [
                'form_params' => [
                    'title' => $title,
                    'description' => $description,
                    'image_url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id'],
                    'user_id' => $userId
                ]
            ]);

            return response()->json(json_decode($artResponse->getBody(), true), $artResponse->getStatusCode());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Request failed',
                'msg' => $e->getMessage()
            ], 500);
        }
    }


    public function forwardDeleteArt($id, Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $user_id = $decoded->sub;

            $client = new \GuzzleHttp\Client();
            $response = $client->delete("http://art-service:8000/arts/{$id}", [
                'headers' => ['X-User-ID' => $user_id]
            ]);

            return response()->json(json_decode($response->getBody()), $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token', 'msg' => $e->getMessage()], 401);
        }
    }

    public function forwardGetArtsByUser($user_id)
    {
        try {
            $client = new Client();
            $response = $client->get("http://art-service:8000/arts/user/{$user_id}");

            return response()->json(json_decode($response->getBody()), $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch user artworks', 'msg' => $e->getMessage()], 500);
        }
    }

    public function forwardGetArtById($id)
    {
        try {
            $client = new Client();
            $response = $client->get("http://art-service:8000/arts/{$id}");

            return response()->json(json_decode($response->getBody()), $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch artwork', 'msg' => $e->getMessage()], 500);
        }
    }
}
