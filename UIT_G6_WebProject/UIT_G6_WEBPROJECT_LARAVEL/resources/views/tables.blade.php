@extends('layout.template')
@section('header')
    @include('layout.header')
@endsection

@section('navbar')
    @include('layout.navbar')
@endsection


@section('content')
    {{-- Content wrapper --}}
    <?php
        
    // get value in with() method return Redirect::to('/tables')->with(['msg'=>'created']);
    $message = session()->get('msg');
    // dd(session());
    if ($message) {
        echo "<div class='notice'><p class='info bg-black text-white ps-3 '>{$message}</p></div>";
    }
    
    ?>
    <div id="content-wrapper" class=" position-relative  d-flex flex-column">
        <div class="form_movie">

            <form class="car updatemovie" action="" method="POST" enctype="multipart/form-data" id="update_movie">
                @csrf
                @method('PUT')
                <div class="closes" onclick="close_form('update_movie')">
                    <i class='bx bx-x'></i>
                </div>
                <h2>Update Manga</h2>
                <label for="name_movie">Name Manga</label>
                <input id="name_movie" placeholder="name moive..." name="title">
                <label for="description">Description</label>
                <textarea id="description" placeholder="description..." name="description"></textarea>

                <div class="d-flex gap-lg-4 gap-sm-1 ">
                    <div class=" d-flex flex-column w-50 ">
                        <label for="category">categories</label>
                        <select id="category" name="category">
                            @foreach ($category as $item)
                                <option value="{{ $item->id }}">{{ $item->genre_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex flex-column w-50 ">
                        <label>Authors</label>
                        <select name="specialgroup" id="specialgroup">
                            @foreach ($specialgroup as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                {{-- <label for="episode_status">Episode Status</label> --}}
                {{-- <input type="number" id="episode_status" placeholder="Insert Episode Status" name="episode_status"> --}}
                <label for="poster_link">Poster Manga</label>
                <input type="file" id="poster_link" placeholder="Insert poster" name="poster_link">
                <button type="submit" name="submit" class=" mt-4 text-white border-0 bg-primary ">Submit</button>


            </form>

            <form id="form_dlt" class="form_delete" action="" method="post" enctype="multipart/form-data">
                @csrf
                @method('delete')
                <h4 class=" fw-bold text-bg-black">Bạn có chắc muốn xóa bộ Manga này không</h4>
                <p>Không thể khôi phục lại sau khi xóa </p>
                <div class="row gap-lg-4 gap-sm-1  ">
                    <button type="submit"
                        class=" bg-danger text-bg-light rounded col-5 btn-outline-danger  ">Delete</button>
                    <button type="reset" id="cancel"
                        class=" bg-success text-bg-light rounded col-5 btn-outline-success">Cancel</button>
                </div>


            </form>

        </div>
        <!-- Main Content -->
        <div id="content" class="mx-lg-4 mx-sm-2">

        <!-- Topbar -->   
        @include('layout.admin_topbar')
        
        <!-- End of Topbar -->



        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-2  text-primary ">Tables</h1>
            <p class="mb-4 text-black-50 ">DataTables are taken from the manga database that is used to generate the
                analysis table below.
                For more information about Movies, please visit <a target="_blank" href="/index">our Manga website</a>.
            </p>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">DataTables Manga</h6>
                    <a href="/export-movie" class="btn bg-warning text-black">Export Excel</a>
                </div>

                <div class="card-body table-movie">

                    <div class="table-responsive">

                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID Manga</th>
                                    <th>Image</th>
                                    <th>Title Manga</th>
                                    <th>Status</th>
                                    <th>Delete Manga</th>
                                    <th>Update Manga</th>
                                </tr>
                            </thead>
                            {{-- <tfoot>
                                <tr>
                                    <th>ID Manga</th>
                                    <th>Image</th>
                                    <th>Title Manga</th>
                                    <th>Status</th>
                                    <th>Delete Manga</th>
                                    <th>Update Manga</th>

                                </tr>
                            </tfoot> --}}
                            <tbody>
                                @foreach ($res as $item)
                                    <tr>
                                        <td>
                                            {{ $item->id }}
                                        </td>
                                        <td>
                                            <img class="w-25 object-cover h-25" src="{{$item->thumb}}" alt="image">
                                        </td>
                                        <td>
                                            {{ $item->title }}
                                        </td>
                                        <td>
                                            {{ $item->status }}
                                        </td>
                                        <td>
                                            <button value="{{ $item->id }}" onclick="update_movie(this)"
                                                class=" d-flex  mx-auto bg-success btn-outline-success text-bg-light rounded update movie ">Update</button>
                                        </td>
                                        <td>
                                            <button value="{{ $item->id }}" onclick="delete_(this,'delete-manga')"
                                                class="d-flex mx-auto bg-danger btn-outline-danger text-bg-light rounded delete ">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- .container-fluid -->
    </div>
    <!-- End of Main Content -->
@endsection

@section('footer')
    @include('layout.script')
    @include('layout.footer')
@endsection
