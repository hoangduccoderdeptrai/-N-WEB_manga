<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListFilmController extends Controller
{
    //
    function get_movie_link()
    {
        // $movie_link = DB::table('movie_link')->get();
        // return view('index', compact('movie_link'));

        // //===
        // $sql = "UPDATE `movie`
        //         JOIN (
        //             SELECT m.`id`, COUNT(DISTINCT CASE WHEN s.`point` > m.`point` THEN s.`point` ELSE NULL END) + 1 AS new_rank
        //             FROM `movie` AS m
        //             LEFT JOIN `movie` AS s ON m.`point` < s.`point`
        //             GROUP BY m.`id`
        //         ) AS subquery ON `movie`.`id` = subquery.`id`
        //         SET `movie`.`rank` = subquery.`new_rank`;
        //         ";

        // DB::statement($sql);
        // top 10 truyên mới nhất
        $manga =DB::table('manga')->take(5)->get();

        // $movies = DB::table('movie_link')
        //     ->join('movie', function ($join) {
        //         $join->on('movie_link.id', '=', 'movie.link_id');
        //     })
        //     ->orderBy('movie.rank')
        //     ->take(10)
        //     ->get();
        //===
        // $data = DB::table('movie_link')
        //     ->join('movie', 'movie_link.id', '=', 'movie.link_id')
        //     ->select('movie_link.*', 'movie.title as movie_name', 'movie.description as movie_api')
        //     ->get();

        // get data
        $all =DB::table('manga')->get();

        // Chuyển đổi dữ liệu thành Collection
        $all = collect($all);

        return view('index', ["data" => $all, "manga" => $manga]);
    }

    public function redirectToMangaDetail($slug,$id)
    {
        $manga = DB::table('manga')->where('id', $id)->first();

        if (!$manga) {
            // Handle case where movie link is not found
            abort(404);
        }

        $chapter = DB::table('chapters')->where('manga_id', $manga->id)->get();
        $genre_name = DB::select("
            SELECT genres.genre_name 
            FROM manga_genres 
            JOIN genres ON manga_genres.genre_id = genres.id
            WHERE manga_genres.manga_id = {$manga->id}
        ");
        // if (!$chapter) {
        //     // Handle case where movie is not found
        //     abort(404);
        // }

        return view('detail',[
            'manga'=>$manga,
            'chapter'=>$chapter,
            'genre_name'=>$genre_name
        ]);
    }
    public function redirectToMangaChapter($slug,$id){
        $manga=DB::table('manga')->where('slug',$slug)->first();
        if(!$manga){
            return view('notfound');
        }
        $get_image =DB::table('cover_images')->where('chapter_id',$id)->orderBy('id','DESC')->get('url');
        $get_chapter =DB::table('chapters')->where('manga_id',$manga->id)->get();
        $get_chapter_name = DB::table('chapters')->where('id',$id)->first();
        if(!$get_chapter_name){
            return view('notfound');
        }
        return view('chapter',[
            'manga'=>$manga,
            'get_chapter'=>$get_chapter,
            'get_chapter_name'=>$get_chapter_name,
            'get_image'=>$get_image
        ]);
    }

    public function redirectToMovieDetail_movies($id)
    {
        return redirect()->route('detailmovies', ['id' => $id]);
    }
}
