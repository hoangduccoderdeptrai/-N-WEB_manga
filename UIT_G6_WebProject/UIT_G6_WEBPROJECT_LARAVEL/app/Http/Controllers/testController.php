<?php

namespace App\Http\Controllers;

use App\Models\test;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmailNotification;
use App\Models\User;
use App\Exports\MoviesExport;
use App\Exports\UsersExport;
use App\Models\user_model;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

function convertTitleToSlug($title)
{
        $slug = Str::slug($title); // Converts the title to a URL-friendly slug
        return $slug;
}
class testController extends Controller
{
    public function home()
    {
        
        return view('home', [
            'heading' => 'khong phai chao',
            'res' => DB::select("SELECT manga.id,title,description,thumb FROM manga 
                                    ORDER BY created_at DESC
                                    LIMIT 0,10")
            


        ]);
    } 

    public function table()
    {
       
        // if (isset($msg)) {
        //     echo $msg;
        // }
        return view('tables', [
            'res' => DB::select("SELECT manga.id,title,thumb,status from manga
                                    "),
            'category' => DB::table('genres')->distinct()->get(),
            'specialgroup' => DB::table('authors')->distinct()->get()
        ]);
    }
    public function add_manga()
    {
        return view('addmanga', [
            'category' => DB::table('genres')->distinct()->get(),
            'author' => DB::table('authors')->distinct()->get()
        ]);
    }
    public function post_manga(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'poster_link' => 'nullable|mimes:png,jpg,jpeg,webp',
            'episode_status' => 'required|max:255'
        ]);
        // create manga
        if ($request->has('poster_link')) {
            $file = $request->file('poster_link'); //$file variable stores the uploaded image file

            $extension = $file->getClientOriginalExtension(); //get jpg or png of image
            $filename = time() . '.' . $extension; //file of image to store into upload directory
            $file->move('uploads/', $filename); //move $file to uploads directory with named is $filename
            $url_poster =url('uploads/'.$filename);

        }
        $insert_manga_id =DB::table('manga')->insertGetId(
            [
                'title'=>$request->title,
                'author_id'=>$request->author,
                'description'=>$request->description,
                'release_date'=>NOW(),
                'status'=>$request->episode_status,
                'thumb'=>$url_poster,
                'created_at'=>NOW(),
                'updated_at'=>NOW()
            ]
            );
        
        if($insert_manga_id){
            
            DB::table('manga_genres')->insert([
                'manga_id'=>$insert_manga_id,
                'genre_id'=>$request->genre
            ]);
        }
        $check =false;
        // validate image grounp
        foreach ($request->all() as $key => $files) {
            // key is chapter ,$files is value
            if (is_array($files)) {
                foreach ($files as $file) {
                    $request->validate([
                        "{$key}.*" => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate each file
                    ]);
                }
                $check=true;
            }
        }
        if($check==false) return Redirect::to('/add-manga')->with(['msg' =>'Add Manga was successful']);
        // Loop through each input group
        try{
            $number_chapter =1;
            foreach ($request->all() as $key => $files) {
                if (is_array($files)) { // Check if the input is an array (multiple files)
                    $fileUrls= [];
                    foreach ($files as $file) {
                        if ($file instanceof UploadedFile) {
                            $extension = $file->getClientOriginalExtension(); //get jpg or png of image
                            $filename = time() . '.' . $extension; //file of image to store into upload directory
                            $file->move('uploads/', $filename); //move $file to uploads directory with named is $filename
                            $url = url('uploads/'.$filename);
                            // add url to the array
                            $fileUrls[] =$url;
                        }
                    }
                    $chapter_id =DB::table('chapters')->insertGetId([
                        'manga_id'=>$insert_manga_id,
                        'chapter_title'=>'Chapter '. $number_chapter,
                        'created_at'=>NOW(),
                        'updated_at'=>NOW()
                    ]);
                    Log::info('File URLs: ' . json_encode($fileUrls));
                    Log::info('Chapter ID: ' . $chapter_id);
                    
                    if($chapter_id){
                        DB::table('cover_images')->insert(
                            array_map(function($url) use ($chapter_id){
                                return ['chapter_id'=> $chapter_id,'url'=>$url];
                            },$fileUrls)
                        );
                    }
                    $number_chapter+=1;
                }
            }
           

        // insert into movie_link table
        // $query_movie = DB::select(
        //     "SELECT * FROM movie_link
        //         WHERE movie_link='{$request->movie_link}' and episode_status='{$request->episode_status}' LIMIT 0,1 "
        // );
        // $query_movie =DB::table('movie_link')->where('movie_link',$request->movie_link)->where('episode_status',$request->episode_status)->limit(1)->get();

        // if (!$query_movie) {
        //     $insert_link = DB::table('movie_link')->insert([
        //         'movie_link' => $request->movie_link,
        //         'poster_link' => "uploads/{$filename}",
        //         'episode_status' => $request->episode_status
        //     ]);
        // } else {

        //     if (File::exists("uploads/{$filename}")) {
        //         File::delete("uploads/{$filename}");
        //     }



        //     return Redirect::to('/add-movie')->with(['msg' => 'The Movie has been existed in the database']);
        // }
        // if ($insert_link) {
        //     $id_link = DB::table('movie_link')->where('movie_link', $request->movie_link)->where('episode_status', $request->episode_status)->distinct()->get('id');

        //     $insert_movie = DB::table('movie')->insert([
        //         'category_id' => $request->category,
        //         'specialgroup_id' => $request->specialgroup,
        //         'title' => $request->title,
        //         'description' => $request->description,
        //         'link_id' => $id_link[0]->id,
        //         'created_at' => NOW(),
        //         'updated_at' => NOW()
        //     ]);
        //     if ($insert_movie) {
            return Redirect::to('/add-manga')->with(['msg' => 'Manga has been created']);
        //     }
        // }
        }catch(\Exception $e){
            error_log($e->getMessage());
            return Redirect::to('/add-manga')->with(['msg' => $e->getMessage()]);
        }
        // $insert_movie = DB::table('movie')->insert([
        //     'status' => $request->episode_status,
        //     'author_id' => $request->specialgroup,
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'created_at' => NOW(),
        //     'updated_at' => NOW()
        // ]);
        // if ($insert_movie) {
        //     return Redirect::to('/add-movie')->with(['msg' => 'Movie has been created']);
        // }

        // return Redirect::to('/add-movie')->with(['msg' => 'Insert Movie was not successful']);
    }

    public function post_manga_test(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'poster_link' => 'nullable|mimes:png,jpg,jpeg,webp',
            'episode_status' => 'required|max:255',
            'genre' => 'required'
        ]);

        try {
            $url_poster = null;

            // Handle Poster Link
            if ($request->has('poster_link')) {
                $file = $request->file('poster_link');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move('uploads/', $filename);
                $url_poster = url('uploads/' . $filename);
            }

            // Insert Manga
            $insert_manga_id = DB::table('manga')->insertGetId([
                'title' => $request->title,
                'author_id' => $request->author,
                'description' => $request->description,
                'release_date' => NOW(),
                'status' => $request->episode_status,
                'slug'=>convertTitleToSlug($request->title),
                'thumb' => $url_poster,
                'created_at' => NOW(),
                'updated_at' => NOW(),
            ]);

            // Insert Genre
            DB::table('manga_genres')->insert([
                'manga_id' => $insert_manga_id,
                'genre_id' => $request->genre,
            ]);
            $check =false;
            // validate image grounp
            foreach ($request->all() as $key => $files) {
                // key is chapter ,$files is value
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $request->validate([
                            "{$key}.*" => 'required|image|mimes:jpeg,png,jpg,gif', // Validate each file
                        ]);
                    }
                    $check=true;
                }
            }
            if($check==false) return Redirect::to('/add-manga')->with(['msg' =>'Add Manga was successful']);
            // Process Chapters
            $number_chapter = 1;
            foreach ($request->all() as $key => $files) {
                if (is_array($files)) {
                    $fileUrls = [];

                    foreach ($files as $file) {
                        if ($file instanceof UploadedFile) {
                            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->move('uploads/', $filename);
                            $fileUrls[] = url('uploads/' . $filename);
                        }
                    }

                    $chapter_id = DB::table('chapters')->insertGetId([
                        'manga_id' => $insert_manga_id,
                        'chapter_title' => 'Chapter ' . $number_chapter,
                        'created_at' => NOW(),
                        'updated_at' => NOW(),
                    ]);

                    $coverImagesData = array_map(function ($url) use ($chapter_id) {
                        return ['chapter_id' => $chapter_id, 'url' => $url];
                    }, $fileUrls);

                    DB::table('cover_images')->insert($coverImagesData);

                    $number_chapter++;
                }
            }

            return Redirect::to('/add-manga')->with(['msg' => 'Manga has been created']);
        } catch (\Exception $e) {
            Log::error('Error adding manga: ' . $e->getMessage());
            return Redirect::to('/add-manga')->with(['msg' => $e->getMessage()]);
        }
    }

    public function post_manga_real(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'poster_link' => 'nullable|mimes:png,jpg,jpeg,webp|max:2048',
            'episode_status' => 'required|max:255',
            'genre' => 'required'
        ]);

        try {
            $url_poster = null;
            DB::beginTransaction();
            // Handle Poster Link
            if ($request->hasFile('poster_link')) {
                $file = $request->file('poster_link');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move('uploads/', $filename);
                $url_poster = url('uploads/' . $filename);
            }

            // Insert Manga
            $insert_manga_id = DB::table('manga')->insertGetId([
                'title' => $request->title,
                'author_id' => $request->author,
                'description' => $request->description,
                'release_date' => now(),
                'status' => $request->episode_status,
                'slug' => convertTitleToSlug($request->title),
                'thumb' => $url_poster,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Genre
            DB::table('manga_genres')->insert([
                'manga_id' => $insert_manga_id,
                'genre_id' => $request->genre,
            ]);

            // Process Chapters
            $number_chapter = 1;
            foreach ($request->file() as $key => $files) {
                if (is_array($files)) {
                    $fileUrls = [];

                    foreach ($files as $file) {
                        if ($file instanceof UploadedFile) {
                            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->move('uploads/', $filename);
                            $fileUrls[] = url('uploads/' . $filename);
                        }
                    }

                    if (!empty($fileUrls)) {
                        $chapter_id = DB::table('chapters')->insertGetId([
                            'manga_id' => $insert_manga_id,
                            'chapter_title' => 'Chapter ' . $number_chapter,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $coverImagesData = array_map(function ($url) use ($chapter_id) {
                            return ['chapter_id' => $chapter_id, 'url' => $url];
                        }, $fileUrls);

                        DB::table('cover_images')->insert($coverImagesData);

                        $number_chapter++;
                    }
                }
            }
            DB::commit();
            return Redirect::to('/add-manga')->with(['msg' => 'Manga has been created']);
        } catch (\Exception $e) {
            Log::error('Error adding manga: ' . $e->getMessage());
            return Redirect::to('/add-manga')->with(['msg' => $e->getMessage()]);
        }
    }




    public function get_manga($id)
    {
        $respose = DB::table('manga')->where('id',$id)->first();
        // $author =DB::table('authors')->where('id',$respose->author_id);
        // $category = DB::ta
          
        
        return response()->json($respose);
    }
    public function update_manga(Request $request, $id)
{
    try {
        // Fetch the manga record by ID
        $record_manga = DB::table('manga')->where('id', $id)->first();

        if (!$record_manga) {
            return Redirect::to('/tables')->with(['msg' => 'Manga not found']);
        }

        $url_poster = $record_manga->thumb; // Default to the existing poster URL

        // Handle poster upload if provided
        if ($request->hasFile('poster_link')) {
            $file = $request->file('poster_link');

            // Validate file type and size
            $request->validate([
                'poster_link' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('uploads'), $filename);

            // // Delete old poster if it exists
            // if (File::exists(public_path($record_manga->thumb))) {
            //     File::delete(public_path($record_manga->thumb));
            // }

            $url_poster = url('uploads/' . $filename);
        }

        // // Update the manga record
        // DB::table('manga')->where('id', $id)->limit(1)->update([
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'release_date' => $record_manga->release_date, // Keep original release date
        //     'status' => $request->episode_status,
        //     'slug' => convertTitleToSlug($request->title),
        //     'thumb' => $url_poster,
        //     'updated_at' => now(),
        // ]);
        DB::table('manga')->where('id', $id)->limit(1)->update([
            'title' => $request->title,
            'description' => $request->description,
            'release_date' => $record_manga->release_date, // Keep original release date
            // 'status' => $request->episode_status,
            'slug' => convertTitleToSlug($request->title),
            'thumb' => $url_poster,
            'updated_at' => now(),
        ]);


        return Redirect::to('/tables')->with(['msg' => 'Manga has been updated']);
    } catch (\Exception $e) {
        Log::error('Error updating manga: ' . $e->getMessage());
        return Redirect::to('/tables')->with(['msg' => 'An error occurred while updating the manga.']);
    }
}


    public function update_manga1(Request $request, $id)
    {
        // return Redirect::to('/tables')->with(['msg'=>'Moive has been updated']);
        try {
            $record_manga = DB::table('manga')->where('id', $id)->first();
            if ($request->has('poster_link')) {
                $file = $request->file('poster_link');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move("uploads/", $filename);
                if (File::exists($record_manga->thumb)) {
                    File::delete($record_manga->thumb);
                }
                $url_poster = url('uploads/' . $filename);
                DB::table('manga')->where('id',$id)->limit(1)->update([
                    'title' => $request->title,
                    // 'author_id' => $request->author,
                    'description' => $request->description,
                    'release_date' => now(),
                    'status' => $request->episode_status,
                    'slug' => convertTitleToSlug($request->title),
                    'thumb' => $url_poster,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('manga')->where('id',$id)->limit(1)->update([
                    'title' => $request->title,
                    // 'author_id' => $request->author,
                    'description' => $request->description,
                    'release_date' => now(),
                    'status' => $request->episode_status,
                    'slug' => convertTitleToSlug($request->title),
                    'updated_at' => now(),
                ]);
                
            }

            // DB::table('movie')->where('id', $record_movie->id)->limit(1)->update([
            //     'category_id' => $request->category,
            //     'specialgroup_id' => $request->specialgroup,
            //     'title' => $request->name_movie,
            //     'description' => $request->description,
            //     'updated_at' => NOW()
            // ]);

            return Redirect::to('/tables')->with(['msg' => 'Movie has been updated']);
        } catch (\Exception $e) {
            return Redirect::to('/tables')->with(['msg' => $e->getMessage()]);
        }
    }
    public function del1ete_manga($id)
    {
        $id_manga = DB::table('manga')->find($id);

        if ($id_manga) {
            // Get all chapter IDs associated with this manga
            $id_chapter = DB::table('chapters')
                ->where('manga_id', $id_manga->id)
                ->pluck('id'); // Extracts an array of chapter IDs

            // Find images related to these chapters
            $images = DB::table('cover_images')
                ->whereIn('chapter_id', $id_chapter)
                ->get(['url']); // Fetch the 'url' column for these images
        }
        try {
            foreach($images as $url){
                if (File::exists($url->url)) {
                    File::delete($url->url);
                }
            }
            // if (File::exists($image->poster_link)) {
            //     File::delete($image->poster_link);
            // }
            DB::table('cover_images')->whereIn('chapter_id', $id_chapter)->delete();
            DB::table('chapters')->where('manga_id',$id_manga)->delete();
            DB::table('movie')->where('id', $id)->distinct()->delete();
            // DB::table('movie_link')->distinct()->delete($id_link_1->link_id);

            return Redirect::to('/tables')->with(['msg' => 'Delete was successfull']);
        } catch (\Exception $e) {
            return Redirect::to('/tables')->with(['msg' => $e->getMessage()]);
        }
    }

    public function delete_manga($id)
    {
        Log::info('Fetching manga record with id: ' . $id);
        try {
            // Fetch the manga record
            $id_manga = DB::table('manga')->find($id);
            if (!$id_manga) {
                return Redirect::to('/tables')->with(['msg' => 'Manga not found']);
            }

            // Get all chapter IDs associated with this manga
            $id_chapter = DB::table('chapters')
                ->where('manga_id', $id_manga->id)
                ->pluck('id');

            if ($id_chapter->isEmpty()) {
                DB::table('manga_genres')->where('manga_id',$id_manga->id)->delete();
                DB::table('manga')->where('id', $id_manga->id)->delete();
                return Redirect::to('/tables')->with(['msg' => 'Delete was successful']);
            }

            // Find images related to these chapters
            $images = DB::table('cover_images')
                ->whereIn('chapter_id', $id_chapter)
                ->get(['url']);

            // Delete related files
            foreach ($images as $image) {
                if (File::exists($image->url)) {
                    File::delete($image->url);
                }
            }

            // Delete related records
            DB::table('cover_images')->whereIn('chapter_id', $id_chapter)->delete();
            DB::table('manga_genres')->where('manga_id',$id_manga->id)->delete();
            DB::table('chapters')->whereIn('id', $id_chapter)->delete();
            DB::table('manga')->where('id', $id_manga->id)->delete();

            return Redirect::to('/tables')->with(['msg' => 'Delete was successful']);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error deleting manga: ' . $e->getMessage());
            return Redirect::to('/tables')->with(['msg' => 'An error occurred while deleting the manga.']);
        }
    }
  

  

    public function add_voucher(Request $request)
    {
        $request->validate([
            'name_voucher' => 'required|max:255',
            'discount' => 'integer'
        ]);

        try {

            DB::table('voucher')->insert([
                'name' => $request->name_voucher,
                'code' => $request->code,
                'discount_percentage' => $request->discount,
                'status' => $request->status,
                'voucherstart_date' => NOW(),
                'voucherend_date' => NOW()

            ]);
            return Redirect::to('/voucher-management')->with(['msg' => 'Voucher was created']);
        } catch (\Exception $e) {
            return Redirect::to('/voucher-management')->with(['msg' => $e->getMessage()]);
        }
    }

    public function get_voucher($id)
    {
        $query_voucher = DB::table('voucher')->where('id', $id)->distinct()->get();
        return response()->json($query_voucher);
    }
    public function delete_voucher($id)
    {
        try {
            DB::table('voucher')->where('id', $id)->distinct()->delete();
            return Redirect::to('/voucher-management')->with(['msg' => 'Voucher was deleted']);
        } catch (\Exception $e) {
            return Redirect::to('/voucher-management')->with(['msg' => $e->getMessage()]);
        }
    }
    public function update_voucher(Request $request, $id)
    {
        try {
            DB::table('voucher')->where('id', $id)->update([
                'name' => $request->name_voucher,
                'code' => $request->code,
                'discount_percentage' => $request->discount,
                'status' => $request->status,
                'voucherstart_date' => NOW(),
                'voucherend_date' => NOW()
            ]);
            return Redirect::to('/voucher-management')->with(['msg' => 'Voucher was Update']);
        } catch (\Exception $e) {
            return Redirect::to('/voucher-management')->with(['msg' => $e->getMessage()]);
        }
    }
    public function users_management()
    {
        return view('users', [
            'res'=>DB::select(" SELECT user.id as id,name,birthday,email,role_type FROM user
            inner join user_role on user.role_id =user_role.id 
            LIMIT 0,15
            "),
            'role'=>DB::table('user_role')->get(['id','role_type'])


        ]);
    }
    public function live_search_users(Request $request)
    {

        try {

            if ($request->has('query')) {

                $query_user = DB::table('user')->where('name', 'like', "{$request->query('query')}%")->orderBy('id')->get();
            } else {
                $query_user = DB::table('user')->limit(10)->offset(0)->orderBy('id')->get();
            }

            return response()->json(['data' => $query_user]);
        } catch (\Exception $e) {
            return response()->json(['msg' => $e->getMessage()]);
        }
    }
    public function live_search_manga(Request $request){
        
        try {

            if ($request->has('query')) {
                $slug =convertTitleToSlug($request->query('query'));
                $query_manga = DB::table('manga')->where('slug', 'like', "{$slug}%")->orderBy('id')->get();
            } else {
                $query_manga = DB::table('manga')->limit(10)->offset(0)->orderBy('id')->get();
            }

            return response()->json(['data' => $query_manga]);
        } catch (\Exception $e) {
            return response()->json(['msg' => $e->getMessage()]);
        }
    }
    public function add_user(Request $request)
    {
        $request->validate([
            'email' => 'email:rfc,dns',
            'avartar' => 'nullable|mimes:png,jpg,jpeg,web',
            'phoneNumber' => 'integer|required'

        ]);
        try {
            if ($request->has('avartar')) {
                $file_image = $request->file('avartar');
                $file_tail = $file_image->getClientOriginalExtension();
                $file_name = time() . "." . $file_tail;
                $file_image->move('avartar/', $file_name);
            }
            DB::table('user')->insert([
                'name' => $request->fullname,
                'birthday' => $request->dayofbirth,
                'email' => $request->email,
                'phoneNumber' => $request->phoneNumber,
                'address' => $request->address,
                'avartar' => "avartar/{$file_name}",
                'role_id' => $request->role_id,
                'plan_id' => 1,
                'created_at' => NOW(),
                'updated_at' => NOW()
            ]);
            return Redirect::to('/users-management')->with(['msg' => 'User was created']);
        } catch (\Exception $e) {
            return Redirect::to('/users-management')->with(['msg' => $e->getMessage()]);
        }
    }
    public function get_user($id){
        $query_user =DB::table('user')->where('id',$id)->distinct()->get();
        return response()->json($query_user);
    }
    public function update_user(Request $request,$id){
        $request->validate([
            'email'=>'email:rfc,dns',
            'avartar'=>'nullable|mimes:png,jpg,jpeg,web',
            'phoneNumber' => 'required|regex:/^\+?[0-9\s\-\(\)]{7,20}$/'

        ]);
        try{
            $avt =DB::table('user')->where('id',$id)->distinct()->get('avartar');//get link image
            // dd($avt[0]->avartar);
            if($request->has('avartar')){
                $file =$request->file('avartar');
                $extension =$file->getClientOriginalExtension();
                $filename =time().'.'.$extension;
                $file->move("avartar/",$filename);
                //delete old file in avartar directory
                if(File::exists($avt[0]->avartar)){
                    File::delete($avt[0]->avartar);
                }
                DB::table('user')->where('id',$id)->limit(1)->update([
                    'avartar'=>"avartar/{$filename}"
                ]);
            }
            $email =DB::table('user')->where('id',$id)->get('email');
            
            DB::table('user')->where('id',$id)->limit(1)->update([
                'name'=>$request->fullname,
                'birthday'=>$request->dayofbirth,
                'email'=>$request->email,
                'phoneNumber'=>$request->phoneNumber,
                'address'=>$request->address,
                'role_id'=>$request->role_id,
                'updated_at'=>NOW()
            ]);
            DB::table('users')->where('email',$email[0]->email)->update([
                'email'=>$request->email,
                'name'=>$request->fullname,
                'updated_at'=>NOW()

            ]);

            return Redirect::to('/users-management')->with(['msg'=>'User was updated']);
           

        }catch(\Exception $e){
            return Redirect::to('/users-management')->with(['msg'=>$e->getMessage()]);
        }
    }
    public function delete_user($id){
        try{
            DB::table('user')->where('id',$id)->distinct()->delete();
            return Redirect::to('/users-management')->with(['msg'=>'User was deleted']);
        }catch(\Exception $e){
            return Redirect::to('/users-management')->with(['msg'=>$e->getMessage()]);
        }
    }

    public function send_mail(){
        return view('sendMail',[
            'res'=>DB::select(" SELECT user.id as id,name,birthday,email,role_type FROM user
                                inner join user_role on user.role_id =user_role.id 
                                LIMIT 0,15
            "),
        ]);
    }

    public function mail_to1(Request $request,$id){
     
        $user =user_model::find($id);
        
        
        $detail=[
            'subject'=>$request->subject,
            'greeting'=>'Dear Sir',
            'content'=>$request->content

        ];
        // Notification::sendNow($data,new SendEmailNotification($detail));
        $user->notify(new SendEmailNotification($detail));
        return Redirect::to('/send-mail')->with(['msg'=>'Send Mail was successful']);
    }
    public function mail_to(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            // Find the user by ID
            $user = user_model::find($id);

            // Notification details
            $detail = [
                'subject' => $validated['subject'],
                'greeting' => 'Dear Sir',
                'content' => $validated['content'],
            ];

            // Notify the user
            $user->notify(new SendEmailNotification($detail));

            return redirect()->route('send-mail')->with('msg', 'Send Mail was successful');
        } catch (\Exception $e) {
            // Handle errors (e.g., user not found or email sending failure)
            return redirect()->route('send-mail')->with('error', 'Failed to send mail: ' . $e->getMessage());
        }
    }

    // export excel
    public function export_user(){
        return Excel::download(new UsersExport(),'users.xlsx');
    }

    public function export_movie(){
        return Excel::download(new MoviesExport(),'movies.xlsx' );
    }

};
