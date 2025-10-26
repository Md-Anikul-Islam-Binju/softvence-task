@extends('admin.app')
@section('admin_content')

    <style>
        body { background:#091427; color:#d6e0ee; }
        .card { background:#0f2436; border:none; border-radius:12px; box-shadow:0 0 12px rgba(0,0,0,0.4); color:inherit; margin-bottom:20px; }
        .card-header { background:#12283e; border-bottom:1px solid rgba(255,255,255,0.1); font-weight:600; }
        .module, .content-item {
            border:1px solid rgba(255,255,255,0.06);
            padding:15px;
            margin-bottom:12px;
            border-radius:10px;
            background:#0b1e33;
        }
        hr { border-color:rgba(255,255,255,0.05); }
        .small-muted { color:#9fb0c6; font-size:0.9rem; }
    </style>

    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title mb-0">All Courses</h4>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @forelse($courses as $course)
            <div class="card mb-4">
                <div class="card-header">{{ $course->title }} ({{ $course->category ?? 'No Category' }})</div>
                <div class="card-body">
                    @if($course->description)
                        <p><strong>Description:</strong><br> {!! nl2br(e($course->description)) !!}</p>
                    @endif

                    @if($course->feature_video_path)
                        <p><strong>Feature Video:</strong></p>
                        <video width="100%" controls>
                            <source src="{{ asset($course->feature_video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif

                    @foreach($course->modules as $module)
                        <div class="module">
                            <strong>Module {{ $loop->iteration }}: {{ $module->title }}</strong>
                            @if($module->description)
                                <p>{!! nl2br(e($module->description)) !!}</p>
                            @endif

                            @foreach($module->contents as $content)
                                <div class="content-item">
                                    <p><strong>Content {{ $loop->iteration }}: {{ $content->title }}</strong></p>
                                    <p><strong>Type:</strong> {{ ucfirst($content->type) }}</p>

                                    @if($content->type == 'text')
                                        <p>{!! nl2br(e($content->body)) !!}</p>
                                    @elseif($content->type == 'link')
                                        <p><a href="{{ $content->body }}" target="_blank">{{ $content->body }}</a></p>
                                    @elseif(in_array($content->type, ['video','image','file']))
                                        @if($content->media_path)
                                            @if($content->type == 'video')
                                                <video width="100%" controls>
                                                    <source src="{{ asset($content->media_path) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @elseif($content->type == 'image')
                                                <img src="{{ asset($content->media_path) }}" class="img-fluid" alt="{{ $content->title }}">
                                            @else
                                                <a href="{{ asset($content->media_path) }}" target="_blank" class="btn btn-sm btn-blue">Download File</a>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p>No courses found.</p>
        @endforelse
    </div>

@endsection
