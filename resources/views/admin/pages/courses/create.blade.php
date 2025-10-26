
@extends('admin.app')
@section('admin_content')

    <style>
        body { background:#091427; color:#d6e0ee; }
        .page-title-box h4 { color: #fff; }
        .card { background:#0f2436; border:none; border-radius:12px; box-shadow:0 0 12px rgba(0,0,0,0.4); color:inherit; }
        .form-control, .form-select { background:#12283e; border:1px solid rgba(255,255,255,0.1); color:#d6e0ee; }
        .form-control:focus, .form-select:focus { background:#12283e; color:#fff; border-color:#1e6fff; box-shadow:none; }
        .form-label { font-weight:500; color:#c5d4e9; }
        .btn-blue { background:#1e6fff; color:#fff; border:none; transition:0.3s; }
        .btn-blue:hover { background:#3b82f6; }
        .btn-outline-danger { border-color:#ff4d4f; color:#ff4d4f; }
        .btn-outline-danger:hover { background:#ff4d4f; color:#fff; }
        .btn-success, .btn-danger { border:none; }
        .small-muted { color:#9fb0c6; font-size:0.9rem; }

        .module, .content-item {
            border:1px solid rgba(255,255,255,0.06);
            padding:15px;
            margin-bottom:12px;
            border-radius:10px;
            background:#0b1e33;
        }

        hr { border-color:rgba(255,255,255,0.05); }

        #addModuleBtn { border-radius:6px; }
        .contents-list { margin-top:8px; }

        .breadcrumb-item a { color:#c9d8f0; }
        .breadcrumb-item.active { color:#fff; }
    </style>

    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-0">Create New Course</h4>
                    <ol class="breadcrumb m-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Softvence</a></li>
                        <li class="breadcrumb-item"><a href="#">Courses</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card p-4">
            <form id="courseForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Course Title <span class="text-danger">*</span></label>
                    <input name="title" id="title" class="form-control" placeholder="Enter course title" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input name="category" id="category" class="form-control" placeholder="Enter category">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Write course overview..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Feature Video (MP4) <small class="text-secondary">optional</small></label>
                    <input type="file" name="feature_video" id="feature_video" accept="video/*" class="form-control">
                    <div class="small-muted mt-1">Prefer compressed mp4 for performance. Max 500MB (server-side limited).</div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0"> Course Modules</h5>
                    <button type="button" id="addModuleBtn" class="btn btn-blue btn-sm">Add Module +</button>
                </div>

                <div id="modulesContainer"></div>

                <div class="mt-4 text-end">
                    <button id="saveBtn" type="submit" class="btn btn-success px-4 me-2"> Save Course</button>
                    <button type="button" id="cancelBtn" class="btn btn-danger px-4">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Module Section --}}
    <template id="moduleTemplate">
        <div class="module">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong> Module <span class="module-index"></span></strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-module">Remove</button>
            </div>

            <div class="mb-2">
                <label class="form-label">Module Title *</label>
                <input class="form-control module-title" placeholder="Enter module title">
            </div>

            <div class="mb-2">
                <label class="form-label">Module Description</label>
                <textarea class="form-control module-description" rows="2" placeholder="Describe this module"></textarea>
            </div>

            <div class="mt-2 mb-2">
                <button type="button" class="btn btn-sm btn-blue add-content-btn">Add Content +</button>
            </div>

            <div class="contents-list"></div>
        </div>
    </template>

    <template id="contentTemplate">
        <div class="content-item">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong> Content <span class="content-index"></span></strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-content">Remove</button>
            </div>

            <div class="mb-2">
                <label class="form-label">Title *</label>
                <input class="form-control content-title" placeholder="Content title">
            </div>

            <div class="mb-2">
                <label class="form-label">Type</label>
                <select class="form-select content-type">
                    <option value="text">Text</option>
                    <option value="video">Video</option>
                    <option value="image">Image</option>
                    <option value="file">File</option>
                    <option value="link">Link</option>
                </select>
            </div>

            <div class="mb-2 content-body-wrapper">
                <label class="form-label">Body / URL / Notes</label>
                <textarea class="form-control content-body" rows="2"></textarea>
            </div>

            <div class="mb-2 content-file-wrapper" style="display:none">
                <label class="form-label">Upload File</label>
                <input type="file" class="form-control content-file-input">
                <div class="small-muted">Attach file if content type is video/image/file.</div>
            </div>
        </div>
    </template>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function(){
            let moduleCount = 0;

            function refreshIndexes() {
                $('#modulesContainer .module').each(function(i){
                    $(this).find('.module-index').text(i + 1);
                    $(this).data('module-index', i);
                    $(this).find('.contents-list .content-item').each(function(j){
                        $(this).find('.content-index').text(j + 1);
                        $(this).data('content-index', j);
                    });
                });
            }

            $('#addModuleBtn').on('click', function(){
                const tpl = $($('#moduleTemplate').html());
                $('#modulesContainer').append(tpl);
                moduleCount++;
                refreshIndexes();
            });

            $(document).on('click', '.remove-module', function(){
                $(this).closest('.module').remove();
                refreshIndexes();
            });

            $(document).on('click', '.add-content-btn', function(){
                const moduleEl = $(this).closest('.module');
                const contentTpl = $($('#contentTemplate').html());
                moduleEl.find('.contents-list').append(contentTpl);
                refreshIndexes();
            });

            $(document).on('click', '.remove-content', function(){
                $(this).closest('.content-item').remove();
                refreshIndexes();
            });

            $(document).on('change', '.content-type', function(){
                const type = $(this).val();
                const parent = $(this).closest('.content-item');
                if (['video','image','file'].includes(type)) {
                    parent.find('.content-file-wrapper').show();
                } else {
                    parent.find('.content-file-wrapper').hide();
                }
                const label = parent.find('.content-body-wrapper label');
                label.text(type === 'link' ? 'Link URL' : 'Body / URL / Notes');
            });

            $('#courseForm').on('submit', function(e){
                e.preventDefault();
                if (!$('#title').val().trim()) {
                    alert('Course title is required');
                    return;
                }

                const fd = new FormData();
                const modules = [];
                const feature = $('#feature_video')[0].files[0];
                if (feature) fd.append('feature_video', feature);

                $('#modulesContainer .module').each(function(mIndex){
                    const $m = $(this);
                    const mTitle = $m.find('.module-title').val().trim();
                    const mDesc = $m.find('.module-description').val().trim();
                    const contents = [];

                    $m.find('.contents-list .content-item').each(function(cIndex){
                        const $c = $(this);
                        const cTitle = $c.find('.content-title').val().trim();
                        const cType = $c.find('.content-type').val();
                        const cBody = $c.find('.content-body').val();
                        const cObj = { title: cTitle, type: cType, body: cBody };

                        const fileInput = $c.find('.content-file-input')[0];
                        if (fileInput?.files?.[0]) {
                            const key = `content_file_module_${mIndex}_content_${cIndex}`;
                            fd.append(key, fileInput.files[0]);
                        }

                        contents.push(cObj);
                    });

                    modules.push({ title: mTitle, description: mDesc, contents });
                });

                fd.append('title', $('#title').val());
                fd.append('category', $('#category').val());
                fd.append('description', $('#description').val());
                fd.append('modules_json', JSON.stringify(modules));

                $.ajax({
                    url: "{{ route('courses.store') }}",
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: fd,
                    processData: false,
                    contentType: false,
                    beforeSend: function(){
                        $('#saveBtn').prop('disabled', true).text('Saving...');
                    },
                    success: function(res){
                        alert('Course created successfully!');
                        window.location.href = "{{ route('courses.index') }}";
                    },
                    error: function(xhr){
                        let msg = 'Failed to save';
                        if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                        else if (xhr.responseJSON?.errors)
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert(msg);
                    },
                    complete: function(){
                        $('#saveBtn').prop('disabled', false).text('Save Course');
                    }
                });
            });

            $('#cancelBtn').on('click', function(){
                if (confirm('Discard changes?')) location.reload();
            });

            // Add one module by default
            $('#addModuleBtn').click();
        });
    </script>

@endsection
