<?php

namespace SecretBox\Http\Controllers;

use Illuminate\Http\Request;
use SecretBox\EncFile;

class EncFileController extends APIController
{
    public function store(Request $request)
    {
        $fileInput = [
            'group_id' => $request->groupId,
            'enc_metadata' => $request->encMetadata
        ];

        $file = new EncFile($fileInput);
        $file->save();

        return $this->sendResponse($file, 'File created successfully.');
    }
}
