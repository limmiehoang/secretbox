<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    private $image_ext = ['jpg', 'jpeg', 'png', 'gif'];
    private $audio_ext = ['mp3', 'ogg', 'mpga'];
    private $video_ext = ['mp4', 'mpeg'];
    private $document_ext = ['doc', 'docx', 'pdf', 'odt'];


    public function index($id = null)
    {
        $model = new File();

        if (!is_null($id)) {
            $response = $model::findOrFail($id);
        } else {
            $records_per_page = 10;

            $files = $model::orderBy('id', 'desc')
                            ->paginate($records_per_page);
            $response = [
                'pagination' => [
                    'total' => $files->total(),
                    'per_page' => $files->perPage(),
                    'current_page' => $files->currentPage(),
                    'last_page' => $files->lastPage(),
                    'from' => $files->firstItem(),
                    'to' => $files->lastItem()
                ],
                'data' => $files
            ];

        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $max_size = 200000;
        $all_ext = implode(',', $this->allExtensions());

        $this->validate($request, [
            'name' => 'required',
            'file' => 'required|file|mimes:' . $all_ext . '|max:' . $max_size
        ]);

        $model = new File();

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $type = $this->getType($ext);

        $storage_folder = '/storage/' . $type . '/';
        $file_name = str_slug($request['name'], '-') . "-" . date("Y-m-d-H-i-s") . '.' . $ext;
        if (Storage::putFileAs('/public/' . $type . '/', $file, $file_name)) {
            return $model::create([
                'name' => $request['name'],
                'type' => $type,
                'extension' => $ext,
                'user_id' => $request['user_id'],
                'group_id' => $request['group_id'],
                'url' => $storage_folder . $file_name
            ]);
        }

        return response()->json(false);
    }

    private function getType($ext)
    {
        if (in_array($ext, $this->image_ext)) {
            return 'image';
        }

        if (in_array($ext, $this->audio_ext)) {
            return 'audio';
        }

        if (in_array($ext, $this->video_ext)) {
            return 'video';
        }

        if (in_array($ext, $this->document_ext)) {
            return 'document';
        }
    }

    private function allExtensions()
    {
        return array_merge($this->image_ext, $this->audio_ext, $this->video_ext, $this->document_ext);
    }
}
