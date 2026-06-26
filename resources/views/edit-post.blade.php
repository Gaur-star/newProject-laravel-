
@extends('layout')

@section('content')

{{-- <div class="row">
    <div class="col-3">

    </div>
    <div class="col-9">

    </div>
</div> --}}

    <div class="form-container">
        <h2>Edit Post</h2>
        @foreach
        <form action="{{ route('post.update', $post->news_post_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="post_id" value="12345">

            <div class="form-group">
                <label for="post-title">Post Title</label>
                <input type="text" id="post-title" name="title" value="" required>
            </div>

            <div class="form-group">
                <label for="post-category">Category</label>
                <select id="post-category" name="category">
                    <option value="web-development" selected>Web Development</option>
                    <option value="design">Design</option>
                    <option value="lifestyle">Lifestyle</option>
                </select>
            </div>

            {{-- <div class="form-group">
                <label for="post-image">Update Featured Image</label>
                <input type="file" id="post-image" name="featured_image" accept="image/*">
                <small class="file-info">Current image: layout-tips.jpg</small>
            </div> --}}

            <div class="form-group">
                <label for="post-content">Content</label>
                <textarea id="post-content" name="content" rows="12" required>Text Here ...</textarea>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-save">Save Changes</button>
                <a href="/dashboard" class="btn btn-cancel">Cancel</a>
            </div>

        </form>
        @endforeach
    </div>



<style>

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 40px 20px;
    /* display: flex; */
    /* justify-content: center; */
}


.form-container {
    background-color: #ffffff;
    width: 100%;
    /* max-width: 700px; */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.form-container h2 {
    margin-top: 0;
    margin-bottom: 24px;
    color: #333333;
    border-bottom: 2px solid #eaeaea;
    padding-bottom: 10px;
}


.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #444444;
}


input[type="text"],
select,
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #cccccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
    color: #333333;
    transition: border-color 0.2s, box-shadow 0.2s;
}

/* Dynamic Interactive Focus Border States */
input[type="text"]:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15);
}

/* Specific Field Enhancements */
textarea {
    resize: vertical;
    font-family: inherit;
    line-height: 1.5;
}

input[type="file"] {
    display: block;
    margin-top: 5px;
}

.file-info {
    display: block;
    color: #777777;
    margin-top: 4px;
    font-size: 13px;
}

/* Action Execution Buttons Grid Alignment */
.button-group {
    display: flex;
    gap: 12px;
    margin-top: 25px;
    border-top: 1px solid #eaeaea;
    padding-top: 20px;
}

.btn {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 4px;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-save {
    background-color: #2ecc71;
    color: #ffffff;
    border: none;
    flex: 2;
}

.btn-save:hover {
    background-color: #27ae60;
}

.btn-cancel {
    background-color: #e74c3c;
    color: #ffffff;
    flex: 1;
}

.btn-cancel:hover {
    background-color: #c0392b;
}

/* Adaptability Rule for Smaller Mobile Display Views */
@media (max-width: 480px) {
    .button-group {
        flex-direction: column;
    }
    .btn {
        width: 100%;
    }
}


</style>





@endsection




