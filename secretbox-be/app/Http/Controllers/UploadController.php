<?php
/**
 * Created by PhpStorm.
 * User: limmie
 * Date: 10/05/2019
 * Time: 10:53
 */

namespace SecretBox\Http\Controllers;


use Illuminate\Http\UploadedFile;
use League\Flysystem\FileNotFoundException;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadController extends APIController
{
    /**
     * Handles the file upload
     *
     * @param FileReceiver $receiver
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws UploadMissingFileException
     *
     */
    public function uploadFile(FileReceiver $receiver)
    {
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();
        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need
            return $this->saveFile($save->getFile());
        }
        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone()
        ]);
    }

    /**
     * Handles the file download
     *
     * @param $id
     *
     * @return StreamedResponse
     * *
     * @throws FileNotFoundException
     *
     */
    public function downloadFile($id)
    {
        //disable execution time limit when downloading a big file.
        set_time_limit(0);

        $fs = \Storage::disk('local')->getDriver();

        $fileName = "private/" . $id;

        $metaData = $fs->getMetadata($fileName);
        $stream = $fs->readStream($fileName);

        if (ob_get_level()) ob_end_clean();

        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
            },
            200,
            [
                'Content-Type' => $metaData['type'],
                'Content-disposition' => 'attachment; filename="' . $metaData['path'] . '"',
            ]);
    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);
        // Build the file path
        $filePath = "private/";
        $finalPath = storage_path("app/" . $filePath);
        // move the file name
        $file->move($finalPath, $fileName);

        return response()->json([
            'path' => $filePath,
            'name' => $fileName
        ]);
    }

    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        return $file->getClientOriginalName();
    }
}