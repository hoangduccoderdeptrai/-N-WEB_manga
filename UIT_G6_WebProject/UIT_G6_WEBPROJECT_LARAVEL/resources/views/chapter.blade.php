<!DOCTYPE html>
<html lang="en">
<?php
    
?>

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        Netflop
    </title>
    <link rel="shortcut icon" type="image/png" href="datasources/img/netflop.png">
    <link>
    <link rel="stylesheet" href="css/style_index.css" />
    <link rel="stylesheet" href="css/style_animation.css" />
    <link rel="stylesheet" href="{{asset('css/custom_web.css')}}" />
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Scripts -->
    <link rel="preload" as="style" href="http://127.0.0.1:8000/build/assets/app-Czjw7esN.css" />
    <link rel="modulepreload" href="http://127.0.0.1:8000/build/assets/app-CrG2wnyX.js" />
    <link rel="stylesheet" href="http://127.0.0.1:8000/build/assets/app-Czjw7esN.css" />
    <script type="module" src="http://127.0.0.1:8000/build/assets/app-CrG2wnyX.js"></script>
    
</head>
@include('layout.user_header')

<body>
    <!-- nav bar -->
    @include('layout.user_navbar')
    {{-- <main style="color: white" class="main-detail"> --}}
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 sidebar">
                    <h4 class="text-warning"><a style="text-decoration:none " class="text-warning" href="/{{$manga->slug}}/{{$manga->id}}" >{{$manga->title}}</a></h4>
                    <p class="text-white">{{$get_chapter_name->chapter_title}}</p>
                    <div class="dropdown mb-3">
                        <button class="btn btn-dark dropdown-toggle w-100" type="button" id="sourceDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Nguồn: VIP #1
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="sourceDropdown">
                            <li><a class="dropdown-item" href="#">Nguồn: VIP #1</a></li>
                            <li><a class="dropdown-item" href="#">Nguồn: VIP #2</a></li>
                        </ul>
                    </div>
                    <!-- Chapter Navigation -->
                    <div class="d-grid gap-2 mb-3">
                        <button class="btn btn-secondary" type="button">&laquo; Previous</button>
                        <button class="btn btn-secondary" type="button">Next &raquo;</button>
                    </div>
                    <!-- Chapter List -->
                    <div class="chapter-list">
                        <h5>Chapters</h5>
                        {{-- <a href="#">Chapter 8</a>
                        <a href="#">Chapter 7</a>
                        <a href="#">Chapter 6</a>
                        <a href="#">Chapter 5</a>
                        <a href="#">Chapter 4</a>
                        <a href="#">Chapter 3</a>
                        <a href="#">Chapter 2</a>
                        <a href="#">Chapter 1</a> --}}
                        @if(!$get_chapter->isEmpty())
                            @foreach($get_chapter as $chapter)
                                <a href="/{{$manga->slug}}/chapter/{{$chapter->id}}">{{$chapter->chapter_title}}</a>
                            @endforeach

                        @else
                            <p>No chapters available for this manga.</p>
                        @endif
                    </div>
                </div>
    
                <!-- Main Content -->
                <div class="col-md-9 chapter-content">
                    <!-- Images Section -->
                    <div class="chapter-image text-center">
                        @if(!$get_image->isEmpty())
                            @foreach($get_image as $image)
                                <img src="{{$image->url}}" alt="Page Image">
                            @endforeach
                        @endif
                        {{-- <!-- Placeholder Images -->
                        <img src="https://via.placeholder.com/800x600?text=1" alt="Page 1">
                        <img src="https://via.placeholder.com/800x600?text=2" alt="Page 2">
                        <img src="https://via.placeholder.com/800x600?text=3" alt="Page 3">
                        <img src="https://via.placeholder.com/800x600?text=4" alt="Page 4">
                        <img src="https://via.placeholder.com/800x600?text=5" alt="Page 5">
                        <img src="https://via.placeholder.com/800x600?text=6" alt="Page 6">
                        <img src="https://via.placeholder.com/800x600?text=7" alt="Page 7">
                        <img src="https://via.placeholder.com/800x600?text=8" alt="Page 8"> --}}
                    </div>
                </div>
            </div>
        </div>
    {{-- </main> --}}
    <!-- nav bar -->
    @include('layout.user_footer');
</body>
<script src="js/logout.js"></script>
<script src="js/script.js"></script>
<script src="js/manga.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>