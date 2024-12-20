<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Netflop</title>
    <link rel="shortcut icon" type="image/png" href="datasources/img/netflop.png">
    <link rel="stylesheet" href="{{asset('css/custom_web.css')}}" />
    <link rel="stylesheet" href="css/style_index.css" />
    <link rel="stylesheet" href="css/style_animation.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap"
        rel="stylesheet" />
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/search/css/style.css" />
    <script src="assets/search/js/global.js" defer></script>
    <script src="assets/search/js/index.js" type="module"></script>
</head>

<body>

    <!-- Navbar -->
    <div class="navbar">
        @include('layout.user_header')
        @include('layout.user_navbar')
    </div>
    <!-- Search Container -->
    <div id="search" style="min-height: 500px; max-height:fit-content; background-color: #151515; color: white;">
        <div class="header">
            <img src="datasources/img/netsearch.png" alt="search" style=" max-width:130px; " class="leading-icon" />
            <div class="search-box" search-box>
                <div class="search-wrapper" search-wrapper>
                    <input id="live_search" style="align-items: center" type="text" name="search"
                        aria-label="search movies" placeholder="Search any Manga..." autocomplete="off"
                        class="search-field" search-field />
                    <img src='http://localhost:8000/chatBot/search.png' width="24" height="24" alt="search"
                        class="leading-icon" />
                </div>
            </div>
        </div>
        <div class="container mt-4" >
            <div class="row">
                <div class="col-lg-12" id="table-data"></div>
            </div>

        </div>
        {{-- <div id="searchResults" style="height: fit-content; padding:10px 10px;"></div> --}}
    </div>


    @include('layout.user_footer')

    {{-- <script src="js/logout.js"></script> --}}
    <script src="js/logout.js"></script>
    <script src="js/script.js"></script>
    <script src="js/manga.js" defer></script>
    <script type="text/javascript">
        // const movieRedirectUrl = '{{ route('movies.redirectmovies', ':id') }}';
        $(document).ready(function(){
            console.log("le hoang duc")
            $("#live_search").keyup(function(){
                var input =$(this).val()
                console.log("test")
                $.ajax({
                    // url:"live_search/live_search_voucher.php",
                    url:'/live-search-manga',
                    data:{query:input}, //this is query parameter like /live-search-voucher?query=input
                    // method:"GET",
                    success:function(response){
                        console.log(response.data.length)
                        var result =response.data
                        var output ="";
                        if(!response.msg){
                            // output =`
                            //     <thead>
                            //         <tr>
                            //             <th>ID</th>
                            //             <th>Name</th>
                            //             <th>BirthDay</th>
                            //             <th>email</th>
                            //             <th>Role</th>
                            //             <th>Edit</th>
                            //             <th>Remove</th>
                            //         </tr>
                            //     </thead>
                            //     </tbody>`
                            // ;
                            output =`
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <p class="result-title">Results for</p>
                                        <div class="row g-3">
                            `

                            
                            
                            result= Array.from(result)
                            result.forEach(element => {
                                output +=`
                                    <div class="col-md-3">
                                        <a href="/${element.slug}/${element.id}" class="card">
                                            <img src="${element.thumb}" class="card-img-top" alt="Manga ${element.id}">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">${element.title}</h5>
                                                
                                            </div>
                                        </a>
                                    </div>
                                `
                            });
                            output+=`       </div>
                                        </div>
                                    </div>
                                
                            `

                            $("#table-data").html(output)
                        }else{
                            $("#table-data").html(response.msg)
                        }
                    }
                })
            })
        })
    </script>
   

</body>

</html>
