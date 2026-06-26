<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NewsPostSites;
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


    // echo "<pre>";
    // print_r($posts);
    // die();
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





    public function syncNews1($siteName = null)
    {
        // set_time_limit(0); 
        // ini_set('memory_limit', '1024M'); 

        // $sites = [
        //     [
        //         'name' => 'spindigit.com',
        //         'url'  => 'https://spindigit.com/'
        //     ]

            
        // ];

        $sites = [
            //  'spindigit' => 'https://spindigit.com/',
            'pronewsreport.com' => 'https://pronewsreport.com/'
            ];

        
            if (!empty($siteName)) {

                if (!isset($sites[$siteName])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid site'
                    ], 400);
                }

                $site = [
                    'name' => $spindigit,
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

        // foreach ($sites as $site) {

        //     $siteFetched = 0;
        //     $siteInserted = 0;
        //     $siteUpdated = 0;

        //     $page = 1;

        //     while (true) {

            //             $api = $site['url'] . "/wp-json/wp/v2/posts?per_page=20&page=" . $page . "&orderby=date&order=desc&_fields=id,date,link,title";
            // // dd($api);

            //             $response = Http::timeout(60) 
                                    // ->withOptions([
                                    //         'verify' => true, 
                                    //         'allow_redirects' => true, 
                                    //     ])
                                    // ->get($api);


            // $body = $response->body();
            // dd($response);

            // $httpCode = $response->status();


            // if ($response->failed()) {

                // $error = $response->toException();

            // }

            // $data = $response->json();
                        /////////////////////////////////////

                        // if ($httpCode == 400 || $httpCode == 404) {
                        //     break;
                        // }

            
        // $response = Http::get($site['url'] . '?page=' . $page);

        // if ($response->failed()) {
        //     $summary[] = [
        //         'site' => $site['name'],
        //         'error' => 'Failed on page ' . $page . 
        //                    ' | HTTP: ' . $response->status() . 
        //                    ' | Error: ' . $response->body()
        //     ];
        // }

        //             $posts = json_decode($response, true);
                    // dd($posts);
                    // if (!is_array($posts) || count($posts) == 0) {
                    //     break;
                    // }

                    // foreach ($posts as $post) {

                    //     $siteFetched++;
                    //     $totalFetched++;

                    //     $title = trim(strip_tags($post['title']['rendered'] ?? ''));

                    // if ($title == '') {
                    //     continue;
                    // }

                    // $key = md5(strtolower($title));

                    // $existingPost = DB::table('news_posts')->where('unique_key', $key)->first();

                    // if (!$existingPost) {
                    //     $newsPostId = DB::table('news_posts')->insertGetId([
                    //         'title' => $title,
                    //         'unique_key' => $key,
                    //         'post_date' => $post['date'] ?? null,
                    //         'created_at' => now(),
                    //         'updated_at' => now()
                    //     ]);
                    //     $siteInserted++;
                    //     $totalInserted++;
                    // } else {
                    //     $newsPostId = $existingPost->id;

                    // DB::table('news_posts')
                    //     ->where('id', $newsPostId)
                    //     ->update([
                    //         'title' => $title,
                    //         'post_date' => $post['date'] ?? $existingPost->post_date,
                    //         'updated_at' => now()
                    //     ]);
                    //     $siteUpdated++;
                    //     $totalUpdated++;
                    // }

                    // $existingSite = DB::table('news_post_sites')
                    //     ->where('news_post_id', $newsPostId)
                    //     ->where('site_name', $site['name'])
                    //     ->first();

                    // if (!$existingSite) {
                    //         DB::table('news_post_sites')->insert([
                    //             'news_post_id' => $newsPostId,
                    //             'site_name' => $site['name'],
                    //             'post_link' => $post['link'] ?? '#',
                    //             'created_at' => now(),
                    //             'updated_at' => now()
                    //         ]);
                    //     } else {
                    //         DB::table('news_post_sites')
                    //             ->where('id', $existingSite->id)
                    //             ->update([
                    //                 'site_name' => $site['name'],
                    //                 'post_link' => $post['link'] ?? $existingSite->post_link,
                    //                 'updated_at' => now()
                    //             ]);
                    //     }
                    // }

            
                    //         if (count($posts) < 100) {
                    //             break;
                    //         }

                    //         $page++;
                    //     }

                    //     $summary[] = [
                    //         'site' => $site['name'],
                    //         'fetched' => $siteFetched,
                    //         'inserted' => $siteInserted,
                    //         'updated' => $siteUpdated
                    //     ];
                    // }

        foreach ($sites as $name => $url) {
        //    dd($sites); 
                $site = [
                    'name' => $name,
                    'url' => $url
                ];
        // dd($site);
                $result = $this->syncSingleSite($site);
        // dd($result);
        // die();
                $summary[] = $result;
                $totalFetched += $result['fetched'];
                $totalInserted += $result['inserted'];
                $totalUpdated += $result['updated'];
            }





        Cache::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'News sync completed',
            'total_fetched' => $totalFetched,
            'total_inserted' => $totalInserted,
            'total_updated' => $totalUpdated,
            'site_summary' => $summary
        ]);
    }


    public function syncNews($siteName = null)
    {
        $sites = [
            //  'spindigit' => 'https://spindigit.com/',
            'pronewsreport.com' => 'https://pronewsreport.com/'
            ];

        
            if (!empty($siteName)) {

                if (!isset($sites[$siteName])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid site'
                    ], 400);
                }

                $site = [
                    'name' => $spindigit,
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
       
                $site = [
                    'name' => $name,
                    'url' => $url
                ];
   
            $result = $this->syncSingleSite($site);
   
                $summary[] = $result;
                $totalFetched += $result['fetched'];
                $totalInserted += $result['inserted'];
                $totalUpdated += $result['updated'];
            }

        // dd($result);



        Cache::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'News sync completed',
            'total_fetched' => $totalFetched,
            'total_inserted' => $totalInserted,
            'total_updated' => $totalUpdated,
            'site_summary' => $summary
        ]);
    }


    private function syncSingleSite($site)
    {

        // echo "<pre>";
        // print_r($site);
        // die();

        $siteFetched = 0;
        $siteInserted = 0;
        $siteUpdated = 0;
        $siteDeactivated = 0;
        $errors = [];

        $seenWpIds = [];

        $page = 1;

        while (true) {

            $api = $site['url'] . "/wp-json/wp/v2/posts?per_page=100&page=" . $page . "&orderby=date&order=desc&_fields=id, date, link, title, content, excerpt, status, featured_media";


            $response = Http::get($api);
            if ($response->successful()) {
                $data = $response->json();
            }

            

            $response = Http::timeout(60)
                ->withoutVerifying()
                ->get($api);
            $httpCode = $response->status();


            if ($httpCode == 400 || $httpCode == 404) {
                break;
            }

            if (!$response->successful()) {
                $errors[] = "Failed on page {$page} | HTTP {$httpCode}";
                break;
            }

            $posts = $response->json();

           

            if (!is_array($posts) || count($posts) == 0) {
                echo "1";
                break;
            }

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
                        'updated_at' => now()->format('Y-m-d')
                    ]);

                    $siteInserted++;
                }
            }

            if (count($posts) < 100) {
                break;
            }

            $page++;
        }


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
/////////////////////////////////////////////////////////////////////
    public function editPost($id){
        // dd($id);
        // return view('edit-post');
        $post = NewsPostSites::where('news_post_id', $id)->firstOrFail();
        // dd($post);
        return view('edit-post', compact('post'));
    }

    public function updatePost(Request $request, $id){
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);
        $post = NewsPostSites::where('news_post_id', $id)->firstOrFail();
        $post->update([
            'post_title' => $request->title,
            'post_content' => $request->content,
        ]);

        return redirect()
                ->route('post.edit', $id )
                ->with('status', 'Post updated');
    }

   



}