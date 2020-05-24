<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $fromDate = $request->has('from_date') ? $request->get('from_date') : now()->subDays(DEFAULT_POST_FILTERING_DAYS);
        $toDate = $request->has('to_date') ? $request->get('to_date') : now();
        return Post::where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate)
            ->when(auth()->user()->role == ROLE_EMPLOYEE, function ($query) {
                return $query->where('user_id', auth()->user()->id);
            }, function ($orWhen) use ($request) {
                if ($request->has('user_id')) {
                    return $orWhen->where('user_id', $request->get('user_id'));
                } else {
                    return $orWhen->whereNotNull('user_id');
                }
            })->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string[]
     */
    public function store(Request $request)
    {
        $fileName = $filePath = null;
        if ($request->has('image')) {
            $image = $request->file('image');
            $fileName = convertFileName($image->getClientOriginalName());
            $image->storeAs('public/post_images', $fileName);
            $filePath = Storage::url('post_images/' . 'screenshot-2020-05-23-at-45524-pm.png' );
        }
        Post::create([
            'user_id' => 1,
            'image_name' => $fileName,
            'image' => $filePath,
            'description' => $request->get('description') ?? null,
        ]);
        return ['message' => 'Created Successfully'];
    }

    /**
     * update a existing resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string[]
     */
    public function update(Request $request, $id)
    {
        Post::where('id', $id)->update([
            'description' => $request->get('description'),
        ]);
        return ['message' => 'Updated Successfully'];
    }
}
