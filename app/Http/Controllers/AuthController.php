<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NewsPostSites;
use App\Models\ListOfSites;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    public function showLogin()
    {
        return view('login');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect('/dashboard');
        }

        return back()->with('error', 'Invalid Email or Password');
    }

    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // return view('layout');
        return view('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function showAddUser(){
        if(!Auth::check()){
            return redirect('/login');
        }
        return view('add-user');
    }

    public function storeUser(Request $request){
        if(!Auth::check()){
            return redirect('/login');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required | email | unique:users',
            'password' => 'required | min:2'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return redirect('/dashboard')->with('success', 'User Added Successfully');
    }
    
    public function userList()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('user-list', compact('users'));
    }

    public function newsDashboard()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $search = request()->get('search');
        $siteFilter = request()->get('site');
        $monthFilter = request()->get('month');
        $yearFilter = request()->get('year');

        // $query = DB::table('news_posts');

        $query = DB::table('news_posts')
                    ->whereIn('id', function ($sub) {
                        $sub->select('news_post_id')
                            ->from('news_post_sites')
                            ->where('is_active', 1);
                    });


        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }


        if (!empty($siteFilter)) {
            $query->whereIn('id', function ($sub) use ($siteFilter) {
                $sub->select('news_post_id')
                    ->from('news_post_sites')
                    ->where('site_name', $siteFilter)
                    ->where('is_active', 1);
            });
        }


        if (!empty($monthFilter)) {
            $query->whereMonth('post_date', $monthFilter);
        }


        if (!empty($yearFilter)) {
            $query->whereYear('post_date', $yearFilter);
        }


        $posts = $query->orderBy('post_date', 'desc')->paginate(20);
        $posts->appends(request()->query());

        $postIds = collect($posts->items())->pluck('id')->toArray();

        $allSites = [];    

        if (!empty($postIds)) {
                $allSites = DB::table('news_post_sites')
                    ->whereIn('news_post_id', $postIds)
                    ->where('is_active', 1)
                    ->orderBy('site_name')
                    ->get()
                    ->groupBy('news_post_id');
            }

        // $siteList = DB::table('news_post_sites')
        //     ->select('site_name')
        //     ->distinct()
        //     ->orderBy('site_name')
        //     ->pluck('site_name');

        $siteList = DB::table('news_post_sites')
                        ->where('is_active', 1)
                        ->select('site_name')
                        ->distinct()
                        ->orderBy('site_name')
                        ->pluck('site_name');

        
        $yearList = DB::table('news_posts')
                        ->selectRaw('YEAR(post_date) as year')
                        ->whereNotNull('post_date')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');

        // foreach ($posts as $post) {
        //     $post->formatted_date = !empty($post->post_date)
        //         ? date('d M Y, h:i A', strtotime($post->post_date))
        //         : '';

        //     $post->sites = $allSites[$post->id] ?? collect([]);
        // }

        foreach ($posts as $post) {
        $post->formatted_date = !empty($post->post_date)
            ? date('d M Y, h:i A', strtotime($post->post_date))
            : '';

        $post->sites = $allSites[$post->id] ?? collect([]);

        // Main display content and main link from first available site
        $post->main_content = '';
        $post->main_link = '#';
        $post->main_image = null;

        if (count($post->sites) > 0) {
            $post->main_content = $post->sites[0]->post_content ?? '';
            $post->main_link = $post->sites[0]->post_link ?? '#';
            $post->main_image = $post->sites[0]->post_image ?? null;
        }

    
    }


        return view('news-dashboard', compact(
            'posts',
            'siteList',
            'search',
            'siteFilter',
            'monthFilter',
            'yearFilter',
            'yearList'
        ));


    }


    public function syncNews($siteName = null)
    {
        // Cache::flush();
        $sites = [

            // 'magazineplus' => 'themagazineplus.com', 
            // 'switchingfashion' => 'switchingfashion.com',
            'yourdigitalwall' => 'yourdigitalwall.com',
            'worldfrontnews' => 'worldfrontnews.com',
            'pronewsreport' => 'pronewsreport.com',
            'spindigit' => 'spindigit.com',
            'yorkpedia' => 'yorkpedia.com'        

            ];

  
            if (!empty($siteName)) {

                if (!isset($sites[$siteName])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid site'
                    ], 400);
                }

                $site = [
                    'name' => $sites[$siteName],
                    'url' => $sites[$siteName]
                ];               

                $result = $this->syncSingleSite($site);

                Cache::flush();

                return response()->json([
                    'status' => 'success',
                    'message' => $siteName . ' sync completed',
                    'result' => $result
                ]);
            }

   

        $summary = [];
        $totalFetched = 0;
        $totalInserted = 0;
        $totalUpdated = 0;
      

        foreach ($sites as $name => $url) {
             \Log::info("Starting sync for: " . $name);
              
                 $site = [
                    'name' => $name,
                    'url' => $url
                ];

                    // dd($site);

            $result = $this->syncSingleSite($site);
   

                $summary[] = $result;
                $totalFetched += $result['fetched'];
                $totalInserted += $result['inserted'];
                $totalUpdated += $result['updated'];
            }



        // Cache::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'News sync completed',
        ]);
    }


    private function syncSingleSite($site)
    {

        $siteFetched = 0;
        $siteInserted = 0;
        $siteUpdated = 0;
        $siteDeactivated = 0;
        $errors = [];

        $seenWpIds = [];

        $page = 1;

        // while (true) {
// do {
            $api = $site['url'] . "/wp-json/wp/v2/posts?per_page=40&page=".$page;
            // $api = "https://switchingfashion.com/wp-json/wp/v2/posts/";

            // $image = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;

            $response = Http::get($api);


//      $response = Http::withHeaders([
//     'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
//     'Accept' => 'application/json',
//     'Referer' => 'https://themagazineplus.com',
// ])->get('https://themagazineplus.com/wp-json/wp/v2/posts/');

// dd($response->status(), $response->body());
// dd($response->status());
// dd($response->body());

            // if ($response->successful()) {
            //     $data = $response->json();
            // }            

            // $response = Http::timeout(60)
            //     ->withoutVerifying()
            //     ->get($api);
            // $httpCode = $response->status();
            // dd($httpCode);
            // if ($httpCode == 400 || $httpCode == 404) {
            //     break;
            // }

            // if (!$response->successful()) {
            //     $errors[] = "Failed on page {$page} | HTTP {$httpCode}";
            //     break;
            // }

            $posts = $response->json();           

            // if (!is_array($posts) || count($posts) == 0) {
            //     echo "1";
            //     break;
            // }
            // dd($posts);
            foreach ($posts as $post) {

                $siteFetched++;

                $wpPostId = $post['id'] ?? null;
                $title = trim(strip_tags($post['title']['rendered'] ?? ''));
                $content = trim(strip_tags($post['content']['rendered'] ?? ''));

                $image = null;

                if (!empty($post['featured_media'])) {
                    try {
                        $mediaResponse = Http::timeout(30)
                            ->withoutVerifying()
                            ->get($site['url'] . "/wp-json/wp/v2/media/" . $post['featured_media']);

                        if ($mediaResponse->successful()) {
                            $mediaData = $mediaResponse->json();
                            $image = $mediaData['source_url'] ?? null;
                        }
                    } catch (\Exception $e) {
                        $image = null;
                    }
                }

                $postDate = $post['date'] ?? null;
                $postLink = $post['link'] ?? '#';
                $postStatus = $post['status'] ?? 'publish';

                if (!$wpPostId || $title == '') {
                    continue;
                }

                $seenWpIds[] = $wpPostId;

                $groupKey = md5(strtolower($title));

                $existingSitePost = DB::table('news_post_sites')
                    ->where('site_name', $site['name'])
                    ->where('wp_post_id', $wpPostId)
                    ->first();

                if ($existingSitePost) {

                    $newsPostId = $existingSitePost->news_post_id;

                    DB::table('news_posts')
                        ->where('id', $newsPostId)
                        ->update([
                            'title' => $title,
                            'unique_key' => $groupKey,
                            'post_date' => $postDate,
                            'updated_at' => now()
                        ]);

                    DB::table('news_post_sites')
                        ->where('id', $existingSitePost->id)
                        ->update([
                            'post_title' => $title,
                            'post_date' => $postDate,
                            'post_link' => $postLink,
                            'post_status' => $postStatus,
                            'is_active' => 1,
                            'updated_at' => now()
                        ]);

                    $siteUpdated++;
                } else {

                    $existingMainPost = DB::table('news_posts')
                        ->where('unique_key', $groupKey)
                        ->first();

                    if ($existingMainPost) {
                        $newsPostId = $existingMainPost->id;

                        DB::table('news_posts')
                            ->where('id', $newsPostId)
                            ->update([
                                'title' => $title,
                                'post_date' => $postDate,
                                'updated_at' => now()
                            ]);
                    } else {
                        $newsPostId = DB::table('news_posts')->insertGetId([
                            'title' => $title,
                            'unique_key' => $groupKey,
                            'post_date' => $postDate,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                  

                    DB::table('news_post_sites')->insert([
                        'news_post_id' => $newsPostId,
                        'wp_post_id' => $wpPostId,
                        'site_name' => $site['name'],
                        'post_title' => $title,
                        'post_content' => $content,
                        'post_image' => $image,
                        'post_date' => $postDate,
                        'post_link' => $postLink,
                        'post_status' => $postStatus,
                        'is_active' => 1,
                        'created_at' => now()->format('Y-m-d'),
                        'updated_at' => now()->format('Y-m-d'),
                        'sync_status' => 'success',
                    ]);

                    $siteInserted++;
                }
            }

        //    $page++;
        //  } while (count($posts) > 0);
        


        if (!empty($seenWpIds)) {
            $deactivated = DB::table('news_post_sites')
                ->where('site_name', $site['name'])
                ->whereNotIn('wp_post_id', $seenWpIds)
                ->where('is_active', 1)
                ->update([
                    'is_active' => 0,
                    'updated_at' => now()
                ]);

            $siteDeactivated = $deactivated;
        }

        return [
            'site' => $site['name'],
            'fetched' => $siteFetched,
            'inserted' => $siteInserted,
            'updated' => $siteUpdated,
            'deactivated' => $siteDeactivated,
            'errors' => $errors
        ];
    }

    public function editPost($id){
        $post = NewsPostSites::where('news_post_id', $id)->firstOrFail();
        return view('edit-post', compact('post'));
    }

    public function updatePost(Request $request, $id){
       
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);
    
        $post = NewsPostSites::where('news_post_id', $id)->firstOrFail();

        $siteName = $post->site_name ;  //https://worldfrontnews.com/

        $post->update([
            'post_title' => $request->title,
            'post_content' => $request->content,
            'sync_status' => 'pending',
            
        ]);
        
        $result = $this->UpdateMainSitePost($id, $siteName);

        if($result->successful()){
                echo "Successfull";

                }else{
                    dd($result->json());
             }

        return redirect()
                ->route('post.edit', $id )
                ->with('status', 'Post updated');
        
                   
    }

    public function sitedetail(){
        return view('siteDetails');
    }

    public function updateSitePosts(){
        $sites = ListOfSites::all();
        return view('updatePage', compact('sites'));

    }


    public function UpdateMainSitePost( $id, $siteName ){

        $posts = NewsPostSites::where('id', $id)
                                ->get();            
        
        foreach($posts as $post){
        
        $user = "editor";
        if( $siteName == 'spindigit' ){                
            $Apassword = "5hN9 Wn2X vzwP Ekt7 8ACl XsTI";
        }

        if( $siteName == 'yorkpedia' ){                
            $Apassword = "nROf 1dOu ItSA kq9f JKgO 6FuI";
        }

        if( $siteName == 'worldfrontnews' ){                
            $Apassword = "NXB2 bWAh 6GIf AzKG uvJW z1YP";
        }

        if( $siteName == 'pronewsreport' ){                
            $Apassword = "oex1 X64d oyZF qgCK xiE2 KIme";
        }

        $Sitename = 'https://' . $siteName . '.com';  //https://spindigit.com/   

        $wp_id = $post->wp_post_id;

            $response = Http::withBasicAuth( $user, $Apassword )
                                        ->put( $Sitename . '/wp-json/wp/v2/posts/' . $wp_id,
                [
                    'title' => $post->post_title,
                    'content' => $post->post_content,
                    'status' => 'pending'
                ]);
        
                return $response;
                }

                                                
    }

   



}