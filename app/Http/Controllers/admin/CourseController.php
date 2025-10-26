<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function create()
    {
        return view('admin.pages.courses.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'feature_video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:512000',
        ]);

        try {
            DB::beginTransaction();

            $course = Course::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
            ]);

            //Upload feature video
            if ($request->hasFile('feature_video')) {
                $file = $request->file('feature_video');
                $fileName = time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/courses/feature_videos/'), $fileName);
                $course->update(['feature_video_path' => 'uploads/courses/feature_videos/'.$fileName]);
            }

            //Decode modules JSON
            $modules = json_decode($request->modules_json, true) ?? [];

            foreach ($modules as $mIndex => $moduleData) {
                $module = $course->modules()->create([
                    'title' => $moduleData['title'] ?? 'Untitled Module',
                    'description' => $moduleData['description'] ?? null,
                    'position' => $mIndex + 1,
                ]);

                foreach ($moduleData['contents'] ?? [] as $cIndex => $contentData) {
                    $content = new Content([
                        'title' => $contentData['title'] ?? 'Untitled Content',
                        'type' => $contentData['type'] ?? 'text',
                        'body' => $contentData['body'] ?? null,
                        'position' => $cIndex + 1,
                    ]);

                    $fileKey = "content_file_module_{$mIndex}_content_{$cIndex}";
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        $fileName = time().'_'.Str::random(5).'.'.$file->getClientOriginalExtension();
                        $file->move(public_path('uploads/courses/contents/'), $fileName);
                        $content->media_path = 'uploads/courses/contents/'.$fileName;
                    }

                    $module->contents()->save($content);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Course created successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course Store Error: '.$e->getMessage());
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    public function index()
    {
        $courses = Course::with(['modules.contents'])->get();
        return view('admin.pages.courses.index', compact('courses'));
    }


}
