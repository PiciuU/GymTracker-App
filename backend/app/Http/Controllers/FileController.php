<?php

namespace App\Http\Controllers;

use App\Models\Ad;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Checks if the user has administrator privileges.
     *
     * @return bool
     */
    public function hasAccess()
    {
        return auth()->user()->hasAdminPrivileges();
    }

    /**
     * Process files and store them.
     *
     * @param array $files
     * @param Exercise $exercise
     * @return array
     */
    private function processFiles($files, $exercise)
    {
        $data = [
            'thumbnailUrl' => $exercise->thumbnail_url,
            'attachmentUrl' => $exercise->attachment_url,
        ];

        foreach ($files as $fieldName => $file) {
            if ($file) {
                $fileName = substr(hash('sha256', time().$fieldName), 0, 16).'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs($exercise->id, $fileName, ['disk' => 'uploads']);
                if ($path === false) {
                    return $this->errorResponse("An error occurred while uploading the file, try again later.", 500);
                }
                $data[$fieldName] = $fileName;
            }
        }

        return $data;
    }

    /**
     * Upload a file for a specific exercise.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function upload($id, Request $request)
    {
        $request->validate([
            'thumbnailFile' => 'sometimes|required|file|mimes:png,jpg,jpeg,webp,mp4,gif,webm|max:10000',
            'attachmentFile' => 'sometimes|required|file|mimes:png,jpg,jpeg,webp,mp4,gif,webm|max:10000',
        ]);

        $exercise = auth()->user()->exercises()->where('id', $id)->first();

        if (!$exercise) return $this->errorResponse("Exercise not found.", 404);

        if (!$request->hasFile('thumbnailFile') && !$request->hasFile('attachmentFile')) return $this->errorResponse("Uploaded file not found.", 400);

        $files = [
            'thumbnailUrl' => $request->file('thumbnailFile'),
            'attachmentUrl' => $request->file('attachmentFile'),
        ];

        $data = $this->processFiles($files, $exercise);

        // DELETE OLD FILES
        if ($data['thumbnailUrl'] != $exercise->thumbnail_url) {
            if (Storage::disk('uploads')->exists($exercise->id.'/'.$exercise->thumbnail_url)) {
                Storage::disk('uploads')->delete($exercise->id.'/'.$exercise->thumbnail_url);
            }
        }
        if ($data['attachmentUrl'] != $exercise->attachment_url) {
            if (Storage::disk('uploads')->exists($exercise->id.'/'.$exercise->attachment_url)) {
                Storage::disk('uploads')->delete($exercise->id.'/'.$exercise->attachment_url);
            }
        }

        $exercise->update([
            'thumbnail_url' => $data['thumbnailUrl'],
            'attachment_url' => $data['attachmentUrl']
        ]);

        return $this->successResponse("Files have been successfully uploaded", ['files' => $data]);
    }

    /**
     * Delete a specific file associated with an exercise.
     *
     * @param int $id
     * @param string $filename
     * @return \App\Http\Traits\ResponseTrait
     */
    public function delete($id, Request $request)
    {
        $exercise = auth()->user()->exercises()->where('id', $id)->first();

        if (!$exercise) return $this->errorResponse("Exercise not found.", 404);

        // DELETE OLD FILES
        if ($request->thumbnailUrl == null && $exercise->thumbnail_url) {
            if (Storage::disk('uploads')->exists($exercise->id.'/'.$exercise->thumbnail_url)) {
                Storage::disk('uploads')->delete($exercise->id.'/'.$exercise->thumbnail_url);
            }
            $exercise->update(['thumbnail_url' => null]);
        }
        if ($request->attachmentUrl == null && $exercise->attachment_url) {
            if (Storage::disk('uploads')->exists($exercise->id.'/'.$exercise->attachment_url)) {
                Storage::disk('uploads')->delete($exercise->id.'/'.$exercise->attachment_url);
            }
            $exercise->update(['attachment_url' => null]);
        }

        return $this->successResponse("Files has been successfully deleted.", ['thumbnailUrl' => $exercise->thumbnail_url, 'attachmentUrl' => $exercise->attachment_url]);
    }
}