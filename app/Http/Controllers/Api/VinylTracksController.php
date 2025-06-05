<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VinylMaster;
use Illuminate\Http\Request;

class VinylTracksController extends Controller
{
    /**
     * Retorna as faixas de um disco de vinil específico
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTracks($id)
    {
        $vinyl = VinylMaster::with('tracks')->findOrFail($id);
        
        $tracks = $vinyl->tracks->map(function ($track) {
            return [
                'id' => $track->id,
                'title' => $track->name, // A coluna no banco é 'name', mas retornamos como 'title'
                'position' => $track->position,
                'duration' => $track->duration,
                'youtube_url' => $track->youtube_url // Usamos diretamente a URL do YouTube
            ];
        })
        ->filter(function ($track) {
            // Filtra apenas faixas com URL do YouTube válida
            return !empty($track['youtube_url']);
        })
        ->sortBy('position')
        ->values();
        
        return response()->json([
            'vinyl_id' => $vinyl->id,
            'vinyl_title' => $vinyl->title,
            'artist' => $vinyl->artists->pluck('name')->implode(', '),
            'cover_image' => asset('storage/' . $vinyl->cover_image),
            'tracks' => $tracks
        ]);
    }
}
