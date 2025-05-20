<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VinylMaster;
use App\Models\Track;

class TrackController extends Controller
{
    public function editTracks($id)
    {
        $vinyl = VinylMaster::with('tracks')->findOrFail($id);
        return view('admin.tracks.edit', compact('vinyl'));
    }

    private function validateTrackData(Request $request)
    {
        return $request->validate([
            'tracks.*.id' => 'nullable|exists:tracks,id',
            'tracks.*.title' => 'required|string|max:255',
            'tracks.*.duration' => 'nullable|string|max:255',
        ]);
    }

    public function updateTracks(Request $request, $id)
    {
        $vinyl = VinylMaster::findOrFail($id);

        $tracks = $request->input('tracks', []);

        foreach ($tracks as $trackData) {
            if (isset($trackData['id'])) {
                $track = Track::find($trackData['id']);
                if ($track) {
                    $track->update($trackData);
                }
            } else {
                $vinyl->tracks()->create($trackData);
            }
        }

        // Delete tracks that are not in the submitted data
        $vinyl->tracks()->whereNotIn('id', array_column($tracks, 'id'))->delete();

        return redirect()->route('admin.vinyls.index')->with('success', 'faixas alteradas com sucesso');
    }
}

